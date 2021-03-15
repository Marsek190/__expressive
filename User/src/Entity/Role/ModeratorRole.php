<?php
declare(strict_types=1);

namespace User\Entity\Role;

use User\Entity\UserAccessProperty;

/**
 * Class ModeratorRole
 * @package User\Entity\Role
 */
class ModeratorRole extends AbstractRole
{
    /**
     * @return int
     */
    public function getAccessProperty(): int
    {
        return UserAccessProperty::MODERATOR;
    }
}