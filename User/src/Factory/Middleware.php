<?php


namespace User\Factory;


use Interop\Container\ContainerInterface;
use User\Middleware\AuthorizationMiddleware;
use User\Middleware\CsrfMiddleware;
use User\Service\ACL;
use User\Service\UserAccess as UserAccessService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\Container as SessionContainer;
use Zend\Session\SessionManager;
use User\Service\User as UserService;
use User\Service\JWT;
use User\Middleware\PermissionsMiddleware;

class Middleware implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        switch ($requestedName) {
            case CsrfMiddleware::class:
                $sessionContainer = new SessionContainer(CsrfMiddleware::class, $container->get(SessionManager::class));
                $handler = new $requestedName($sessionContainer);
                break;
            case AuthorizationMiddleware::class:
                $sessionContainer = new SessionContainer(AuthorizationMiddleware::class, $container->get(SessionManager::class));
                $handler = new $requestedName(
                    $sessionContainer,
                    $container->get(ACL::class),
                    $container->get(UserAccessService::class),
                    $container->get(UserService::class),
                    $container->get(JWT::class)
                );
                break;
            case PermissionsMiddleware::class:
                $handler = new $requestedName(
                    $container->get(UserAccessService::class)
                );
                break;
            default:
                $handler = new $requestedName();
                break;
        }

        return $handler;
    }
}