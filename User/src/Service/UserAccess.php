<?php
declare(strict_types=1);

namespace User\Service;


use Core\Entity\User\AccessPermission;
use Core\Entity\User\UserAccessProperty;
use Fig\Http\Message\StatusCodeInterface;
use Skeleton\Entity\Collection\ArrayList;
use User\Mapper\UserAccess as UserAccessMapper;
use Core\Entity\User\UserAccess as UserAccessEntity;

/**
 * Class UserAccess
 * @package User\Service
 */
class UserAccess
{
    protected UserAccessMapper $userAccess;

    /**
     * UserAccess constructor.
     * @param UserAccessMapper $userAccess
     */
    public function __construct(UserAccessMapper $userAccess)
    {
        $this->userAccess = $userAccess;
    }

    /**
     * @param int $userId
     * @return int
     */
    public function getHigherAccessByUserId(int $userId): int
    {
        /** @var AccessPermission $userAccess */
        $userAccess = $this->userAccess->getHigherAccessByUserId($userId) ?: null;
        if (is_null($userAccess)) {
            return UserAccessProperty::BLOCKED;
        }
        return $userAccess->getAccess();
    }

    /**
     * @param int $access
     * @return ArrayList
     */
    public function getPermissionList(int $access): ArrayList
    {
        $result = $this->userAccess->getAllPermission($access);
        $accessSection = [];
        /**
         * @var UserAccessEntity $userAccess
         */
        foreach ($result as $userAccess) {
            $accessSection[$userAccess->getAccess()][] = $userAccess->getSection();
        }
        return new ArrayList($accessSection);
    }

    public function saveOrFail(AccessPermission $accessPermission): AccessPermission
    {
        $saved = $this->userAccess->save($accessPermission) ?: null;
        if (is_null($saved)) {
            throw new \RuntimeException(sprintf(
                "Failure save record access_permission's with id = %s.",
                $accessPermission->getId()),
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }
        return $saved;
    }
}