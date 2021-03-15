<?php
declare(strict_types=1);

namespace User\Entity\Role;


use Core\Entity\User\UserAccessProperty;
use Skeleton\Entity\Collection\ArrayList;
use SplFixedArray;

/**
 * Class AbstractRole
 * @package User\Entity\Role
 */
abstract class AbstractRole
{
    protected ArrayList $permissionList;

    protected SplFixedArray $childedRoles;

    /**
     * @return ArrayList
     */
    public function getPermissionList(): ArrayList
    {
        return $this->permissionList;
    }

    /**
     * @param ArrayList $permissionList
     * @return AbstractRole
     */
    public function setPermissionList(ArrayList $permissionList): AbstractRole
    {
        $this->permissionList = $permissionList;
        return $this;
    }

    /**
     * @return SplFixedArray
     */
    public function getChildedRoles(): SplFixedArray
    {
        return $this->childedRoles;
    }

    /**
     * @param SplFixedArray $childedRoles
     * @return AbstractRole
     */
    public function setChildedRoles(SplFixedArray $childedRoles): AbstractRole
    {
        $this->childedRoles = $childedRoles;
        return $this;
    }

    /**
     * @param string $section
     * @return bool
     */
    public function isAllowed(string $section): bool
    {
        return $this->permissionList->offsetExists($section);
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->getAccessProperty() == UserAccessProperty::ADMIN;
    }

    /**
     * @return bool
     */
    public function isBlocked(): bool
    {
        return $this->getAccessProperty() == UserAccessProperty::BLOCKED;
    }

    /**
     * @return int
     */
    abstract public function getAccessProperty(): int;
}