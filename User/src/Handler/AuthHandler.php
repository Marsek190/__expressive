<?php
declare(strict_types=1);

namespace User\Handler;


use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Front\Middleware\HeaderMenu;
use Front\Middleware\Seo;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Skeleton\Entity\BaseInterface\DateTimeInterface;
use Core\Entity\User\AbstractRootEntity;
use User\Entity\AuthRequest;
use Core\Entity\User\User as UserEntity;
use User\Exception\UserExistsError;
use User\Exception\WrongCredentialsError;
use User\Middleware\CsrfMiddleware;
use User\Response\Fail;
use User\Response\Success;
use User\Service\Auth as AuthService;
use User\Form\Auth as AuthForm;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Session\Container as SessionContainer;

class AuthHandler extends AbstractActionHandler
{
    protected TemplateRendererInterface $templateRenderer;

    protected SessionContainer $sessionContainer;

    protected AuthService $authService;

    protected AuthForm $authForm;

    public function __construct(
        TemplateRendererInterface $templateRenderer,
        AuthService $authService,
        SessionContainer $sessionContainer,
        AuthForm $authForm)
    {
        $this->templateRenderer = $templateRenderer;
        $this->authService = $authService;
        $this->sessionContainer = $sessionContainer;
        $this->authForm = $authForm;
    }

    /**
     * Аутентифицирует пользователя по заданным эл. адресу и паролю.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function loginAction(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // если пользователь уже авторизован
        if ($this->sessionContainer->offsetExists(UserEntity::class)) {
            return new RedirectResponse($this->redirectBase, StatusCodeInterface::STATUS_FOUND);
        }
        if ($request->getMethod() == RequestMethodInterface::METHOD_POST) {
            $userAuthData = $request->getParsedBody();
            $authRequest = new AuthRequest();
            $this->authForm
                ->bind($authRequest)
                ->setData($userAuthData);

            if ($this->authForm->isValid()) {
                try {
                    $user = $this->authService
                        ->setAuthRequest($authRequest)
                        ->authenticate();

                } catch (WrongCredentialsError | UserExistsError $error) {
                    return Fail::fromStringMessage($error->getMessage());

                } catch (\RuntimeException $e) {
                    return Fail::fromStringMessage($e->getMessage(), StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
                }

                if ($authRequest->isRememberMe()) {
                    $currentTimestampWithAddedMonth = time() + DateTimeInterface::SECONDS_IN_A_MONTH;
                    setcookie(AbstractRootEntity::USER_TOKEN_COOKIE, $user->getAuthKey(), $currentTimestampWithAddedMonth);
                }
                return new Success();
            }
            return Fail::fromArrayMessages($this->authForm->getMessages());
        }

        $this->templateRenderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'seo', $request->getAttribute(Seo::class));
        $this->templateRenderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'headerTopMenu', $request->getAttribute(HeaderMenu::class));
        $this->templateRenderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, CsrfMiddleware::GUARD_ATTRIBUTE, $request->getAttribute(CsrfMiddleware::class));

        return new HtmlResponse($this->templateRenderer->render(UserEntity::class . '::login'));
    }

    /**
     * Выход из аккаунта
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function logoutAction(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // если пользователь не авторизован
        if (! $this->sessionContainer->offsetExists(UserEntity::class)) {
            return new RedirectResponse('', StatusCodeInterface::STATUS_FOUND);
        }
        $this->sessionContainer->getManager()->destroy();
        setcookie(AbstractRootEntity::USER_TOKEN_COOKIE, false);

        return new RedirectResponse($this->redirectBase, StatusCodeInterface::STATUS_FOUND);
    }
}