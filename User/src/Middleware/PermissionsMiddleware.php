<?php
declare(strict_types=1);

namespace User\Middleware;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Core\Entity\User\User as UserEntity;
use User\Response\RedirectBack;
use User\Service\ACL;

/**
 * Class PermissionsMiddleware
 * @package User\Middleware
 */
class PermissionsMiddleware implements MiddlewareInterface
{
    protected ACL $acl;

    /**
     * PermissionsMiddleware constructor.
     * @param ACL $acl
     */
    public function __construct(ACL $acl)
    {
        $this->acl = $acl;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $section = $request->getAttribute('section');
        /** @var UserEntity|null $user */
        $user = $request->getAttribute(UserEntity::class);
        // если нет привязке к разделу - идем дальше
        if (is_null($section)) {
            return $handler->handle($request);
        }
        // если пользователь не аутентифицирован или у пользователя нет прав
        if (is_null($user) || $this->acl->getPermissionListSelfAndChildes($user->getAcl())->offsetExists($section)) {
            return new RedirectBack($request);
        }
    }
}