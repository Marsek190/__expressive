<?php
declare(strict_types=1);

namespace User\Handler;


use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Front\Middleware\HeaderMenu;
use Front\Middleware\Seo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use User\Entity\RestorePasswordRequest;
use Core\Entity\User\User as UserEntity;
use User\Exception\FieldValidationError;
use User\Exception\UserExistsError;
use User\Exception\WrongSecureCodeError;
use User\Form\Registration;
use User\Form\RestorePassword as RestorePasswordForm;
use User\Middleware\CsrfMiddleware;
use User\Response\Fail;
use User\Service\ChangePassword;
use User\Service\RequestForChangePassword;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Session\Container as SessionContainer;

/**
 * Class RestorePasswordHandler
 * @package User\Handler
 */
class RestorePasswordHandler extends AbstractActionHandler
{
    protected TemplateRendererInterface $templateRenderer;

    protected SessionContainer $sessionContainer;

    protected RestorePasswordForm $restorePasswordForm;

    protected ChangePassword $changePassword;

    protected RequestForChangePassword $requestForChangePassword;

    protected string $redirectIfAuthenticated = '/';

    /**
     * RestorePasswordHandler constructor.
     * @param TemplateRendererInterface $templateRenderer
     * @param SessionContainer $sessionContainer
     * @param RestorePasswordForm $restorePasswordForm
     * @param RequestForChangePassword $requestForChangePassword
     * @param ChangePassword $changePassword
     */
    public function __construct(
        TemplateRendererInterface $templateRenderer,
        SessionContainer $sessionContainer,
        RestorePasswordForm $restorePasswordForm,
        RequestForChangePassword $requestForChangePassword,
        ChangePassword $changePassword)
    {
        $this->templateRenderer = $templateRenderer;
        $this->sessionContainer = $sessionContainer;
        $this->restorePasswordForm = $restorePasswordForm;
        $this->requestForChangePassword = $requestForChangePassword;
        $this->changePassword = $changePassword;
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
        if ($request->getMethod() == RequestMethodInterface::METHOD_GET) {
            $this->templateRenderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'seo', $request->getAttribute(Seo::class));
            $this->templateRenderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'headerTopMenu', $request->getAttribute(HeaderMenu::class));
            $this->templateRenderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, CsrfMiddleware::GUARD_ATTRIBUTE, $request->getAttribute(CsrfMiddleware::class));
        }

        return parent::process($request, $handler);
    }

    public function forgotAction(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() == RequestMethodInterface::METHOD_POST) {
            $email = $request->getParsedBody()[Registration::EMAIL_PROPERTY];
            try {
                $this->requestForChangePassword
                    ->setBaseUrl($this->getBaseUrl($request))
                    ->setUserEmail($email)
                    ->requestForChangePassword();
            } catch (FieldValidationError | UserExistsError $error) {
                return Fail::fromStringMessage($error->getMessage());

            } catch (\RuntimeException $e) {
                return Fail::fromStringMessage($e->getMessage(), StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
            }

            // мыбы сенд редирект на страницу с "на вашу почту отправлено письмо с blah blah blah..." ?
            return new RedirectResponse('/password-restore-static', StatusCodeInterface::STATUS_FOUND);
        }

        return new HtmlResponse($this->templateRenderer->render(UserEntity::class . '::reset-password'));
    }

    public function changeAction(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() == RequestMethodInterface::METHOD_POST) {
            $restorePasswordData = array_merge($request->getParsedBody(), $request->getQueryParams());
            $restorePasswordRequest = new RestorePasswordRequest();
            $this->restorePasswordForm
                ->bind($restorePasswordRequest)
                ->setData($restorePasswordData);

            if ($this->restorePasswordForm->isValid()) {
                try {
                    $this->changePassword
                        ->setRestorePasswordRequest($restorePasswordRequest)
                        ->changePassword();
                } catch (WrongSecureCodeError | UserExistsError $error) {
                    return Fail::fromStringMessage($error->getMessage());

                } catch (\RuntimeException $e) {
                    return Fail::fromStringMessage($e->getMessage(), StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
                }

                return new RedirectResponse('/login', StatusCodeInterface::STATUS_FOUND);
            }
            return Fail::fromArrayMessages($this->restorePasswordForm->getMessages());
        }

        return new HtmlResponse($this->templateRenderer->render(UserEntity::class . '::restore-password'));
    }
}