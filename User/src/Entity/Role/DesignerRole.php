<?php
declare(strict_types=1);

namespace User\Entity\Role;


use Core\Entity\User\UserAccessProperty;

/**
 * Class DesignerRole
 * @package User\Entity\Role
 */
class DesignerRole extends AbstractRole
{
    /**
     * @return int
     */
    public function getAccessProperty(): int
    {
        return UserAccessProperty::DESIGNER;
    }
}