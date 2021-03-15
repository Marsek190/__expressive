<?php
declare(strict_types=1);

namespace User\Service;


use Fig\Http\Message\StatusCodeInterface;
use Core\Entity\User\AbstractRootEntity;
use Core\Entity\User\RestorePassword;
use User\Entity\UserRestorePassword;
use User\Mapper\User as UserMapper;
use Core\Entity\User\User as UserEntity;

/**
 * Class User
 * @package User\Service
 */
class User
{
    protected UserMapper $userMapper;

    /**
     * User constructor.
     * @param UserMapper $userMapper
     */
    public function __construct(UserMapper $userMapper)
    {
        $this->userMapper = $userMapper;
    }

    /**
     * @param int $userId
     * @return UserEntity|null
     */
    public function getById(int $userId): ?UserEntity
    {
        return $this->userMapper->getById($userId) ?: null;
    }

    /**
     * @param string $userEmail
     * @return UserEntity|null
     */
    public function getByEmail(string $userEmail): ?UserEntity
    {
        return $this->userMapper->getByEmail($userEmail) ?: null;
    }

    /**
     * @param string $userName
     * @return UserEntity|null
     */
    public function getByName(string $userName): ?UserEntity
    {
        return $this->userMapper->getByName($userName) ?: null;
    }

    /**
     * @param UserEntity $user
     * @return void
     * @throws \RuntimeException
     */
    public function updateOrFailAuthKey(UserEntity $user): void
    {
        try {
            $this->userMapper->updateAuthKey($user);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf(
                "Failure updated column user's with id = %s auth_key.",
                $user->getId()),
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param UserEntity $user
     * @return void
     * @throws \RuntimeException
     */
    public function updateOrFailPasswordHash(UserEntity $user): void
    {
        try {
            $this->userMapper->updatePasswordHash($user);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf(
                "Failure updated column user's with id = %s password_hash.",
                $user->getId()),
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param AbstractRootEntity $user
     * @return AbstractRootEntity
     */
    public function saveOrFail(AbstractRootEntity $user): AbstractRootEntity
    {
        $saved = $this->userMapper->save($user) ?: null;
        if (is_null($saved)) {
            throw new \RuntimeException(sprintf(
                "Failure save record user's with id = %s.",
                $user->getId()),
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }
        return $saved;
    }

    /**
     * @param string $userEmail
     * @return UserRestorePassword|null
     */
    public function getUserRestorePasswordByEmail(string $userEmail): ?UserRestorePassword
    {
        return $this->userMapper->getUserRestorePasswordByEmail($userEmail) ?: null;
    }
}