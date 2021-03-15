<?php


namespace User\Factory;


use Interop\Container\ContainerInterface;
use Lcobucci\JWT\Configuration;
use User\Entity\Mail\SenderConfiguration;
use User\Mapper\User as UserMapper;
use User\Mapper\RestorePassword as RestorePasswordMapper;
use User\Service\ACL;
use User\Service\ChangePassword;
use User\Service\JWT;
use User\Service\Mail\RenderedEmail;
use User\Service\Mail\SenderResettingEmailMessage;
use User\Service\UserRegistration;
use User\Service\User as UserService;
use User\Service\RequestForChangePassword;
use User\Service\RestorePassword as RestorePasswordService;
use User\Service\UserAccess as UserAccessService;
use User\Service\Auth as AuthService;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Mail\Transport\Sendmail;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use User\Mapper\UserAccess as UserAccessMapper;

class Service implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config')[SenderResettingEmailMessage::class];
        $sessionContainer = new Container(UserRegistration::class, $container->get(SessionManager::class));
        switch ($requestedName) {
            case ACL::class:
                $service = new $requestedName($container->get(UserAccessService::class));
                break;
            case UserRegistration::class:
                $service = new $requestedName(
                    $container->get(UserService::class),
                    $sessionContainer,
                    $container->get(JWT::class),
                    $container->get(UserAccessService::class)
                );
                break;
            case JWT::class:
                $service = new $requestedName($container->get(Configuration::class));
                break;
            case RenderedEmail::class:
                $service = new $requestedName(
                    $container->get(TemplateRendererInterface::class),
                    $config['template']
                );
                break;
            case SenderResettingEmailMessage::class:
                $senderConf = (new SenderConfiguration())
                    ->setSubject($config['subject'])
                    ->setFrom($config['from']);
                $service = new $requestedName(
                    $container->get(Sendmail::class),
                    $container->get(RenderedEmail::class),
                    $senderConf
                );
                break;
            case AuthService::class:
                $service = new $requestedName(
                    $sessionContainer,
                    $container->get(UserService::class),
                    $container->get(JWT::class)
                );
                break;
            case UserService::class:
                $service = new $requestedName($container->get(UserMapper::class));
                break;
            case ChangePassword::class:
                $service = new $requestedName(
                    $container->get(UserService::class),
                    $container->get(RestorePasswordService::class)
                );
                break;
            case RestorePasswordService::class:
                $service = new $requestedName($container->get(RestorePasswordMapper::class));
                break;
            case RequestForChangePassword::class:
                $service = new $requestedName(
                    $container->get(UserService::class),
                    $container->get(RestorePasswordService::class),
                    $container->get(SenderResettingEmailMessage::class),
                    $container->get('password-change')
                );
                break;
            case UserAccessService::class:
                $service = new $requestedName(
                    $container->get(UserAccessMapper::class)
                );
                break;
            default:
                $service = new $requestedName();
                break;
        }

        return $service;
    }
}