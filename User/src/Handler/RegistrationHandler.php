<?php
declare(strict_types=1);

namespace User\Handler;


use Fig\Http\Message\StatusCodeInterface;
use Fig\Http\Message\RequestMethodInterface;
use Front\Middleware\HeaderMenu;
use Front\Middleware\Seo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Skeleton\Entity\BaseInterface\DateTimeInterface;
use Core\Entity\User\AbstractRootEntity;
use User\Exception\RegistrationError;
use User\Middleware\CsrfMiddleware;
use User\Response\Fail;
use User\Response\Success;
use User\Service\UserRegistration;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Session\Container as SessionContainer;
use User\Form\Registration as RegistrationForm;
use Core\Entity\User\User as UserEntity;
use User\Entity\RegistrationRequest;

/**
 * Class RegistrationHandler
 * @package User\Handler
 */
class RegistrationHandler implements MiddlewareInterface
{
    protected UserRegistration $userRegistration;

    protected SessionContainer $sessionContainer;

    protected TemplateRendererInterface $templateRenderer;

    protected RegistrationForm $registrationForm;

    protected string $redirectIfAuthenticated = '/';

    /**
     * RegistrationHandler constructor.
     * @param TemplateRendererInterface $templateRenderer
     * @param UserRegistration $userRegistration
     * @param SessionContainer $sessionContainer
     * @param RegistrationForm $registrationForm
     */
    public function __construct(
        TemplateRendererInterface $templateRenderer,
        UserRegistration $userRegistration,
        SessionContainer $sessionContainer,
        RegistrationForm $registrationForm)
    {
        $this->userRegistration = $userRegistration;
        $this->sessionContainer = $sessionContainer;
        $this->templateRenderer = $templateRenderer;
        $this->registrationForm = $registrationForm;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // если пользователь уже авторизован
        if ($this->sessionContainer->offsetExists(UserEntity::class)) {
            return new RedirectResponse($this->redirectIfAuthenticated, StatusCodeInterface::STATUS_FOUND);
        }
        if ($request->getMethod() == RequestMethodInterface::METHOD_POST) {
            $userRegistrationData = $request->getParsedBody();
            $registrationRequest = new RegistrationRequest();
            $this->registrationForm
                ->bind($registrationRequest)
                ->setData($userRegistrationData);

            if ($this->registrationForm->isValid()) {
                try {
                    $userEntity = $this->userRegistration
                        ->setRegistrationRequest($registrationRequest)
                        ->register();

                } catch (RegistrationError $registrationError) {
                    return Fail::fromStringMessage($registrationError->getMessage());

                } catch (\RuntimeException $e) {
                    return Fail::fromStringMessage($e->getMessage(), StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
                }

                if ($registrationRequest->isRememberMe()) {
                    $currentTimestampWithAddedMonth = time() + DateTimeInterface::SECONDS_IN_A_MONTH;
                    setcookie(AbstractRootEntity::USER_TOKEN_COOKIE, $userEntity->getAuthKey(), $currentTimestampWithAddedMonth);
                }
                return new Success();
            }
            return Fail::fromArrayMessages($this->registrationForm->getMessages());
        }

        $this->templateRenderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'seo', $request->getAttribute(Seo::class));
        $this->templateRenderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'headerTopMenu', $request->getAttribute(HeaderMenu::class));
        $this->templateRenderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, CsrfMiddleware::GUARD_ATTRIBUTE, $request->getAttribute(CsrfMiddleware::class));

        return new HtmlResponse($this->templateRenderer->render(UserEntity::class . '::register'));
    }
}