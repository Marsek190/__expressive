<?php
declare(strict_types=1);

namespace User\Entity\Role;


use Core\Entity\User\UserAccessProperty;

/**
 * Class BlockedUserRole
 * @package User\Entity\Role
 */
class BlockedUserRole extends AbstractRole
{
    /**
     * @return int
     */
    public function getAccessProperty(): int
    {
        return UserAccessProperty::BLOCKED;
    }
}