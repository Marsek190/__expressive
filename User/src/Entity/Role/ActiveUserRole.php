<?php
declare(strict_types=1);

namespace User\Entity\Role;


use Core\Entity\User\UserAccessProperty;

/**
 * Class ActiveUser
 * @package User\Entity\Role
 */
class ActiveUserRole extends AbstractRole
{
    /**
     * @return int
     */
    public function getAccessProperty(): int
    {
        return UserAccessProperty::ACTIVE;
    }
}