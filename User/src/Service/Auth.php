<?php
declare(strict_types=1);

namespace User\Service;


use User\Entity\AuthRequest;
use Core\Entity\User\User as UserEntity;
use Core\Entity\User\UserAccessProperty;
use User\Exception\UserExistsError;
use User\Exception\WrongCredentialsError;
use User\Service\User as UserService;
use Zend\Crypt\Password\Bcrypt;
use Zend\Session\Container as SessionContainer;

/**
 * Class Auth
 * @package User\Service
 */
class Auth
{
    protected AuthRequest $authRequest;

    protected SessionContainer $sessionContainer;

    protected UserService $userService;

    protected JWT $jwt;

    /**
     * Auth constructor.
     * @param SessionContainer $sessionContainer
     * @param User $userService
     * @param JWT $jwt
     */
    public function __construct(SessionContainer $sessionContainer, UserService $userService, JWT $jwt)
    {
        $this->sessionContainer = $sessionContainer;
        $this->userService = $userService;
        $this->jwt = $jwt;
    }

    /**
     * @return UserEntity
     * @throws WrongCredentialsError
     * @throws UserExistsError
     * @throws \RuntimeException
     */
    public function authenticate(): UserEntity
    {
        $user = $this->getUserByEmail();
        if (is_null($user)) {
            throw new UserExistsError(sprintf(
                "User with this e-mail %s does not exist.",
                $this->authRequest->getEmail()
            ));
        }
        if ($this->isPasswordVerifiedFail($user)) {
            throw new UserExistsError(sprintf(
                "User with this password %s does not exist.",
                $this->authRequest->getPassword()
            ));
        }
        if ($this->isBlockedUser($user)) {
            throw new WrongCredentialsError(sprintf(
                "User with name %s has blocked.",
                $user->getUserName()));
        }
        $now = new \DateTimeImmutable();
        $parsedToken = $this->jwt->getParsedToken($user->getAuthKey());
        // если срок действия токена уже истек
        if (! $parsedToken->isExpired($now)) {
            $this->userService->updateOrFailAuthKey($user->setAuthKey($this->jwt->createRememberToken($user)));
        }
        $this->sessionContainer->offsetSet(UserEntity::class, $user->getId());

        return $user;
    }

    /**
     * @return UserEntity|null
     */
    protected function getUserByEmail(): ?UserEntity
    {
        return $this->userService->getByEmail($this->authRequest->getEmail());
    }

    /**
     * @param UserEntity $user
     * @return bool
     */
    protected function isPasswordVerifiedFail(UserEntity $user): bool
    {
        $crypt = new Bcrypt();
        $passwordHash = $user->getPasswordHash();
        return ! $crypt->verify($this->authRequest->getPassword(), $passwordHash);
    }

    /**
     * @param UserEntity $user
     * @return bool
     */
    protected function isBlockedUser(UserEntity $user): bool
    {
        return $user->getAccess() == UserAccessProperty::BLOCKED;
    }

    /**
     * @return AuthRequest
     */
    public function getAuthRequest(): AuthRequest
    {
        return $this->authRequest;
    }

    /**
     * @param AuthRequest $authRequest
     * @return Auth
     */
    public function setAuthRequest(AuthRequest $authRequest): Auth
    {
        $this->authRequest = $authRequest;
        return $this;
    }
}