<?php
declare(strict_types=1);

namespace User\Entity\Role;


use Core\Entity\User\UserAccessProperty;

/**
 * Class AdminRole
 * @package User\Entity\Role
 */
class AdminRole extends AbstractRole
{
    /**
     * @return int
     */
    public function getAccessProperty(): int
    {
        return UserAccessProperty::ADMIN;
    }
}