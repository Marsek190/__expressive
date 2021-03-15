<?php
declare(strict_types=1);

namespace User\Middleware;


use Core\Entity\User\UserAccessProperty;
use ExpressiveLogger\LoggerFacade;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Skeleton\Entity\BaseInterface\DateTimeInterface;
use Core\Entity\User\AbstractRootEntity;
use Core\Entity\User\User as UserEntity;
use User\Service\ACL;
use User\Service\ACLResourceCreator;
use User\Service\JWT;
use User\Service\User as UserService;
use User\Service\UserAccess as UserAccessService;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Session\Container as SessionContainer;
use Lcobucci\JWT\Validation\InvalidToken as InvalidTokenException;

/**
 * Class AuthorizationMiddleware
 * @package User\Middleware
 */
class AuthorizationMiddleware implements MiddlewareInterface
{
    protected SessionContainer $sessionContainer;

    protected UserAccessService $userAccess;

    protected UserService $userService;

    protected ACL $acl;

    protected JWT $jwt;

    /**
     * AuthorizationMiddleware constructor.
     * @param SessionContainer $sessionContainer
     * @param ACL $acl
     * @param UserAccessService $userAccess
     * @param UserService $userService
     * @param JWT $jwt
     */
    public function __construct(
        SessionContainer $sessionContainer,
        ACL $acl,
        UserAccessService $userAccess,
        UserService $userService,
        JWT $jwt)
    {
        $this->sessionContainer = $sessionContainer;
        $this->userService = $userService;
        $this->acl = $acl;
        $this->userAccess = $userAccess;
        $this->jwt = $jwt;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $cookies = $request->getCookieParams();
        // если пользователь не аутентифицирован - идем дальше
        if (is_null($cookies[AbstractRootEntity::USER_TOKEN_COOKIE])) {
            return $handler->handle($request);
        }

        $parsedToken = $this->jwt->getParsedToken($cookies[AbstractRootEntity::USER_TOKEN_COOKIE]);
        try {
            $this->jwt->verifyOrFailRememberToken($parsedToken);
        } catch (InvalidTokenException $e) {
            // логируем попытку авторизации с невалидным токеном
            LoggerFacade::error(sprintf(
                "Invalid token: %s\n %s",
                $e->getMessage(),
                $parsedToken));

            return new EmptyResponse(StatusCodeInterface::STATUS_FORBIDDEN);
        }
        // если пользователь не связан с токеном также сендим 403
        $user = $this->userService->getByName((string) $parsedToken->headers()->get(JWT::USER_NAME_HEADER));
        if (is_null($user)) {
            return new EmptyResponse(StatusCodeInterface::STATUS_FORBIDDEN);
        }
        $acl = ACLResourceCreator::getACLViaAccessProperty($user->getHigherAccess());
        $user->setAcl($this->acl->setPermissionsForEveryRole($acl));

        // если у пользователя роль станет UserAccessProperty::BLOCKED, нужно удалить сессию пользователя и токен
        if ($user->getAcl()->isBlocked()) {
            $this->sessionContainer->getManager()->destroy();
            setcookie(AbstractRootEntity::USER_TOKEN_COOKIE, false);
            return $handler->handle($request);
        }
        $now = new \DateTimeImmutable();
        // если срок действия истек - генерим новый токен
        if (! $parsedToken->isExpired($now)) {
            try {
                $this->userService->updateOrFailAuthKey($user->setAuthKey($this->jwt->createRememberToken($user)));
            } catch (\RuntimeException $e) {
                return new EmptyResponse(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
            }
            $currentTimestampWithAddedMonth = time() + DateTimeInterface::SECONDS_IN_A_MONTH;
            setcookie(AbstractRootEntity::USER_TOKEN_COOKIE, $user->getAuthKey(), $currentTimestampWithAddedMonth);
        }
        if (! $this->sessionContainer->offsetExists(UserEntity::class)) {
            $this->sessionContainer->offsetSet(UserEntity::class, $user->getId());
        }

        return $handler->handle($request->withAttribute(UserEntity::class, $user));
    }
}