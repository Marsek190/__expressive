<?php
declare(strict_types=1);

namespace User\Service;


use Core\Entity\User\AccessPermission;
use Core\Entity\User\UserAccessProperty;
use Core\Entity\User\User as UserEntity;
use User\Entity\UserRegistration as UserRegistrationEntity;
use User\Exception\RegistrationError;
use User\Service\User as UserService;
use User\Entity\RegistrationRequest;
use Zend\Crypt\Password\Bcrypt;
use Zend\Session\Container as SessionContainer;
use User\Service\UserAccess as UserAccessService;

/**
 * Class UserRegistration
 * @package User\Service
 */
class UserRegistration
{
    protected RegistrationRequest $registrationRequest;

    protected UserService $userService;

    protected SessionContainer $sessionContainer;

    protected JWT $jwt;

    protected UserAccessService $userAccess;

    /**
     * @param UserService $userService
     * @param SessionContainer $sessionContainer
     * @param JWT $jwt
     * @param UserAccessService $userAccess
     */
    public function __construct(
        UserService $userService,
        SessionContainer $sessionContainer,
        JWT $jwt,
        UserAccessService $userAccess)
    {
        $this->userService = $userService;
        $this->sessionContainer = $sessionContainer;
        $this->jwt = $jwt;
        $this->userAccess = $userAccess;
    }

    /**
     * @return UserRegistrationEntity
     * @throws RegistrationError
     * @throws \RuntimeException
     */
    public function register(): UserRegistrationEntity
    {
        if ($this->userNameIsAlreadyExists()) {
            throw new RegistrationError(sprintf(
                'User with name %s already exists.',
                $this->registrationRequest->getUserName()));
        }
        if ($this->emailIsAlreadyExists()) {
            throw new RegistrationError(sprintf(
                'Email %s already exists.',
                $this->registrationRequest->getEmail()));
        }
        $userRegistration = $this->addUserRegistration();
        $this->sessionContainer->offsetSet(UserEntity::class, $userRegistration->getId());

        return $userRegistration;
    }

    /**
     * @return bool
     */
    protected function emailIsAlreadyExists(): bool
    {
        return ! is_null($this->userService->getByEmail($this->registrationRequest->getEmail()));
    }

    /**
     * @return bool
     */
    protected function userNameIsAlreadyExists(): bool
    {
        return ! is_null($this->userService->getByName($this->registrationRequest->getEmail()));
    }

    /**
     * @param string $password
     * @return string
     */
    protected function createPasswordHash(string $password): string
    {
        $crypt = new Bcrypt();
        return $crypt->create($password);
    }

    /**
     * @return UserRegistrationEntity
     * @throws \RuntimeException
     */
    protected function addUserRegistration(): UserRegistrationEntity
    {
        $currentRegistrationTimestamp = time();
        $userRegistrationEntity = (new UserRegistrationEntity())
            ->setUserName($this->registrationRequest->getUserName())
            ->setPasswordHash($this->createPasswordHash($this->registrationRequest->getPassword()))
            ->setUserEmail($this->registrationRequest->getEmail())
            ->setRegTime($currentRegistrationTimestamp)
            ->setActTime($currentRegistrationTimestamp);

        $userRegistrationEntity
            ->setAuthKey($this->jwt->createRememberToken($userRegistrationEntity));

        $userCreated = $this->userService->saveOrFail($userRegistrationEntity);
        $userAccess = (new AccessPermission())
            ->setUserId($userCreated->getId())
            ->setAccess(UserAccessProperty::ACTIVE);
        $this->userAccess->saveOrFail($userAccess);

        return $userCreated;
    }

    /**
     * @return null|RegistrationRequest
     */
    public function getRegistrationRequest(): RegistrationRequest
    {
        return $this->registrationRequest;
    }

    /**
     * @param RegistrationRequest $registrationRequest
     * @return UserRegistration
     */
    public function setRegistrationRequest(RegistrationRequest $registrationRequest): UserRegistration
    {
        $this->registrationRequest = $registrationRequest;
        return $this;
    }
}