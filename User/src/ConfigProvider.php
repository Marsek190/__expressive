<?php
declare(strict_types=1);

namespace User;


use Ctrlweb\Expressive\ZendDbWrapper\ZendDbMapperFactory;
use Lcobucci\JWT\Configuration;
use User\Factory\ClassMethodsHydrator;
use User\Factory\JWTConfiguration;
use User\Factory\Middleware;
use User\Factory\Form;
use User\Factory\Handler;
use User\Factory\Service;
use User\Handler\AuthHandler;
use User\Handler\RegistrationHandler;
use User\Handler\RestorePasswordHandler;
use User\Mapper\RestorePassword;
use User\Mapper\User;
use User\Mapper\UserAccess as UserAccessMapper;
use User\Middleware\PermissionsMiddleware;
use User\Service\ACL;
use User\Service\ChangePassword;
use User\Service\RequestForChangePassword;
use User\Service\User as UserService;
use User\Middleware\AuthorizationMiddleware;
use User\Middleware\CsrfMiddleware;
use User\Service\Auth as AuthService;
use User\Service\JWT;
use User\Service\Mail\RenderedEmail;
use User\Service\Mail\SenderResettingEmailMessage;
use User\Service\UserRegistration;
use User\Service\RestorePassword as RestorePasswordService;
use User\Service\UserAccess as UserAccessService;
use Zend\Expressive\Helper\UrlHelperFactory;
use User\Form\Auth as AuthForm;
use Zend\Hydrator\ClassMethods;
use Zend\InputFilter\InputFilter;
use Zend\Session\Service\SessionManagerFactory;
use Zend\Session\SessionManager;
use User\Form\Registration as RegistrationForm;
use User\Form\RestorePassword as RestorePasswordForm;
use Zend\Mail\Transport\Sendmail;

class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates()
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [
                InputFilter::class => InputFilter::class,
            ],
            'factories' => [
                // handlers
                RegistrationHandler::class => Handler::class,
                AuthHandler::class => Handler::class,
                RestorePasswordHandler::class => Handler::class,

                // middleware
                CsrfMiddleware::class => Middleware::class,
                AuthorizationMiddleware::class => Middleware::class,
                PermissionsMiddleware::class => Middleware::class,

                // forms
                RegistrationForm::class => Form::class,
                RestorePasswordForm::class => Form::class,
                AuthForm::class => Form::class,

                // mappers
                User::class => ZendDbMapperFactory::class,
                RestorePassword::class => ZendDbMapperFactory::class,
                UserAccessMapper::class => ZendDbMapperFactory::class,

                // services
                UserRegistration::class => Service::class,
                RenderedEmail::class => Service::class,
                SenderResettingEmailMessage::class => Service::class,
                UserService::class => Service::class,
                ChangePassword::class => Service::class,
                RequestForChangePassword::class => Service::class,
                RestorePasswordService::class => Service::class,
                JWT::class => Service::class,
                UserAccessService::class => Service::class,
                AuthService::class => Service::class,
                ACL::class => Service::class,

                // jwt
                Configuration::class => JWTConfiguration::class,

                // zend-packages
                SessionManager::class => SessionManagerFactory::class,
                ClassMethods::class => ClassMethodsHydrator::class,
                Sendmail::class => \User\Factory\Sendmail::class,
                'password-change' => new UrlHelperFactory(),
            ],
        ];
    }

    /**
     * Returns the templates configuration
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                \Core\Entity\User\User::class => [__DIR__ . '/../templates/layout'],
                'mail' => [__DIR__ . '/../templates/mail'],
            ],
        ];
    }
}

