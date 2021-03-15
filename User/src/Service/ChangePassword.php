<?php
declare(strict_types=1);

namespace User\Service;


use Core\Entity\User\RestorePassword as RestorePasswordEntity;
use User\Entity\RestorePasswordRequest;
use Core\Entity\User\User as UserEntity;
use User\Exception\UserExistsError;
use User\Exception\WrongSecureCodeError;
use User\Service\RestorePassword as RestorePasswordService;
use User\Service\User as UserService;
use Zend\Crypt\Password\Bcrypt;

/**
 * Class ChangePassword
 * @package User\Service
 */
class ChangePassword
{
    protected RestorePasswordRequest $restorePasswordRequest;

    protected UserService $userService;

    protected RestorePasswordService $restorePasswordService;

    /**
     * ChangePassword constructor.
     * @param UserService $userService
     * @param RestorePasswordService $restorePasswordService
     */
    public function __construct(UserService $userService, RestorePasswordService $restorePasswordService)
    {
        $this->userService = $userService;
        $this->restorePasswordService = $restorePasswordService;
    }

    /**
     * @return UserEntity
     * @throws UserExistsError
     * @throws WrongSecureCodeError
     * @throws \RuntimeException
     */
    public function changePassword(): UserEntity
    {
        $user = $this->getUserById();
        if (is_null($user)) {
            throw new UserExistsError(sprintf(
                "User with this id %s was not exists.",
                $this->restorePasswordRequest->getUserId()
            ));
        }
        // если secure_code из реквеста не совпал с кодом из таблицы
        if ($this->isNotEqualSecureCodes()) {
            throw new WrongSecureCodeError("Incorrect recovery link.");
        }
        $crypt = new Bcrypt();
        $passwordHash = $crypt->create($this->restorePasswordRequest->getPassword());
        $this->userService->updateOrFailPasswordHash($user->setPasswordHash($passwordHash));

        return $user;
    }

    protected function getUserById(): ?UserEntity
    {
        return $this->userService->getById($this->restorePasswordRequest->getUserId());
    }

    protected function getRestorePasswordByUserId(): ?RestorePasswordEntity
    {
        return $this->restorePasswordService->getByUserId($this->restorePasswordRequest->getUserId());
    }

    protected function isNotEqualSecureCodes(): bool
    {
        return 0 !== strcasecmp($this->getRestorePasswordByUserId()->getSecureCode(), $this->restorePasswordRequest->getSecureCode());
    }

    /**
     * @return RestorePasswordRequest
     */
    public function getRestorePasswordRequest(): RestorePasswordRequest
    {
        return $this->restorePasswordRequest;
    }

    /**
     * @param RestorePasswordRequest $restorePasswordRequest
     * @return ChangePassword
     */
    public function setRestorePasswordRequest(RestorePasswordRequest $restorePasswordRequest): ChangePassword
    {
        $this->restorePasswordRequest = $restorePasswordRequest;
        return $this;
    }
}