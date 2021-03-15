<?php
declare(strict_types=1);

namespace User\Service;


use Skeleton\Entity\Collection\ArrayList;
use User\Entity\Role\AbstractRole;
use User\Service\UserAccess as UserAccessService;

/**
 * Class ACL
 * @package User\Service
 */
class ACL
{
    protected UserAccessService $userAccess;

    /**
     * ACL constructor.
     * @param UserAccess $userAccess
     */
    public function __construct(UserAccessService $userAccess)
    {
        $this->userAccess = $userAccess;
    }

    /**
     * @param AbstractRole $role
     * @return AbstractRole
     */
    public function setPermissionsForEveryRole(AbstractRole $role): AbstractRole
    {
        $childesRole = $role->getChildedRoles();
        $accessSection = $this->userAccess->getPermissionList($role->getAccessProperty());
        /**
         * @var AbstractRole $childedRole
         */
        foreach ($childesRole as &$childedRole) {
            $childedRole->setPermissionList(new ArrayList($accessSection->offsetGet($childedRole->getAccessProperty())));
        }
        unset($childedRole);
        return $role->setPermissionList($accessSection->offsetGet($role->getAccessProperty()))->setChildedRoles($childesRole);
    }

    /**
     * @param AbstractRole $role
     * @return ArrayList
     */
    public function getPermissionListSelfAndChildes(AbstractRole $role): ArrayList
    {
        $permissionList = $role->getPermissionList();
        /**
         * @var AbstractRole $childedRole
         */
        foreach ($role->getChildedRoles() as $childedRole) {
            /**
             * @var string $permission
             */
            foreach ($childedRole->getPermissionList() as $permission) {
                $permissionList->push($permission);
            }
        }
        return $permissionList->getUniqueValues();
    }
}