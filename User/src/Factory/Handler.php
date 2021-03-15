<?php


namespace User\Factory;


use Interop\Container\ContainerInterface;
use User\Handler\AuthHandler;
use User\Handler\RegistrationHandler;
use User\Handler\RestorePasswordHandler;
use User\Service\UserRegistration;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\Container as SessionContainer;
use User\Form\Registration as RegistrationForm;
use Zend\Session\SessionManager;
use User\Service\Auth as AuthService;
use User\Form\Auth as AuthForm;
use User\Form\RestorePassword as RestorePasswordForm;
use User\Service\RequestForChangePassword;
use User\Service\ChangePassword;

class Handler implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $sessionContainer = new SessionContainer(UserRegistration::class, $container->get(SessionManager::class));
        switch ($requestedName) {
            case RegistrationHandler::class:
                $handler = new $requestedName(
                    $container->get(TemplateRendererInterface::class),
                    $container->get(UserRegistration::class),
                    $sessionContainer,
                    $container->get(RegistrationForm::class)
                );
                break;
            case AuthHandler::class:
                $handler = new $requestedName(
                    $container->get(TemplateRendererInterface::class),
                    $container->get(AuthService::class),
                    $sessionContainer,
                    $container->get(AuthForm::class)
                );
                break;
            case RestorePasswordHandler::class:
                $handler = new $requestedName(
                    $container->get(TemplateRendererInterface::class),
                    $sessionContainer,
                    $container->get(RestorePasswordForm::class),
                    $container->get(RequestForChangePassword::class),
                    $container->get(ChangePassword::class)
                );
                break;
            default:
                $handler = new $requestedName();
                break;
        }

        return $handler;
    }
}