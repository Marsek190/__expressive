<?php
declare(strict_types=1);

namespace User\Service;


use User\Entity\Role\AbstractRole;
use User\Entity\Role\ActiveUserRole;
use User\Entity\Role\AdminRole;
use User\Entity\Role\BlockedUserRole;
use User\Entity\Role\DesignerRole;
use User\Entity\Role\ModeratorRole;
use Core\Entity\User\UserAccessProperty;

/**
 * Class ACLResource
 * @package User\Service
 */
final class ACLResourceCreator
{
    private static string $defaultAcl = BlockedUserRole::class;

    private static array $aclClassMap = [
        UserAccessProperty::ACTIVE => ActiveUserRole::class,
        UserAccessProperty::DESIGNER => DesignerRole::class,
        UserAccessProperty::MODERATOR => ModeratorRole::class,
        UserAccessProperty::ADMIN => AdminRole::class
    ];

    private static array $withoutChildedRoles = [
        UserAccessProperty::BLOCKED
    ];

    /**
     * @param int $access
     * @return AbstractRole
     */
    public static function getACLViaAccessProperty(int $access): AbstractRole
    {
        /** @var AbstractRole $acl */
        $acl = is_null(static::$aclClassMap[$access]) ? new static::$defaultAcl() : new static::$aclClassMap[$access]();
        $aclList = [];
        foreach (static::$aclClassMap as $accessProp => $instance) {
            if ($accessProp < $access) {
                $aclList[] = new $instance();
            }
        }
        return $acl->setChildedRoles(\SplFixedArray::fromArray($aclList));
    }
}