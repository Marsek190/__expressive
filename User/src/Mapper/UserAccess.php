<?php
declare(strict_types=1);

namespace User\Mapper;


use Core\Entity\User\UserAccessProperty;
use Zend\Db\Sql\Where;

/**
 * Class UserAccess
 * @package User\Mapper
 */
class UserAccess extends \Core\Mapper\User\UserAccess
{
    /**
     * @param int $userId
     * @return mixed
     */
    public function getHigherAccessByUserId(int $userId)
    {
        $where = new Where();
        $where->equalTo('user_id', $userId);

        $select = $this->sql->select();
        $select->columns(['access'], false);
        $select->order('access desc');
        $select->limit(1);

        $prepared = $this->sql->prepareStatementForSqlObject($select);
        $result = $this->returnResult($prepared->execute());

        return $result->current();
    }

    /**
     * @param int $access
     * @return mixed
     */
    public function getAllPermission(int $access)
    {
        $this->objectPrototype = \Core\Entity\User\UserAccess::class;
        $tableName = $this->getTableName();

        $where = new Where();
        $where->greaterThan('access', UserAccessProperty::BLOCKED)
            ->and
            ->lessThanOrEqualTo('access', $access);

        $select = $this->sql->select();
        $select->columns([
            "user_id" => "{$tableName}.user_id",
            "access_id" => "section_access.id",
            "section" => "section_access.section",
            "access" => "{$tableName}.access",
        ], false);
        $on = "{$tableName}.id = section_access.access_id";
        $select->join('section_access', $on, [], $select::JOIN_INNER);
        $select->group("section_access.section");
        $select->where($where);

        $prepared = $this->sql->prepareStatementForSqlObject($select);
        $result = $this->returnResult($prepared->execute());

        $this->objectPrototype = \Core\Entity\User\AccessPermission::class;

        return $result;
    }
}