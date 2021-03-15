<?php
declare(strict_types=1);

namespace User\Service;


use Fig\Http\Message\StatusCodeInterface;
use User\Mapper\RestorePassword as RestorePasswordMapper;
use Core\Entity\User\RestorePassword as RestorePasswordEntity;
use Core\Entity\User\User as UserEntity;

/**
 * Class RestorePassword
 * @package User\Service
 */
class RestorePassword
{
    protected RestorePasswordMapper $restorePassword;

    public function __construct(RestorePasswordMapper $restorePassword)
    {
        $this->restorePassword = $restorePassword;
    }

    /**
     * @param RestorePasswordEntity $restorePassword
     * @return void
     * @throws \RuntimeException
     */
    public function updateOrFailSecureCodeAndUpdatedFields(RestorePasswordEntity $restorePassword): void
    {
        try {
            $this->restorePassword->updateSecureCodeAndUpdatedFields($restorePassword);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf(
                "%s",
                $restorePassword->getUserId()),
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param RestorePasswordEntity $restorePassword
     * @return RestorePasswordEntity
     * @throws \RuntimeException
     */
    public function saveOrFail(RestorePasswordEntity $restorePassword): RestorePasswordEntity
    {
        $saved = $this->restorePassword->save($restorePassword);
        if (false === $saved) {
            throw new \RuntimeException(sprintf(
                "%s",
                $restorePassword->getUserId()),
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }
        return $saved;
    }

    /**
     * @param int $userId
     * @return RestorePasswordEntity|null
     */
    public function getByUserId(int $userId): ?RestorePasswordEntity
    {
        return $this->restorePassword->getByUserId($userId) ?: null;
    }
}