<?php
declare(strict_types=1);

namespace User\Mapper;


use Core\Entity\User\User as UserEntity;
use User\Entity\UserRestorePassword;
use Zend\Db\Sql\Where;

/**
 * Class User
 * @package User\Mapper
 */
class User extends \Core\Mapper\User\User
{
   /**
     * @param string $email
     * @return mixed
     */
    public function getByEmail(string $email)
    {
        $tableName = $this->getTableName();
        $where = new Where();
        $where->equalTo('user_email', $email);

        $select = $this->sql->select();
        $select->columns([$select::SQL_STAR], false);
        $on = "access_permission.user_id = {$tableName}.id";
        $select->join("access_permission", $on, ['higher_access' => 'access'], $select::JOIN_LEFT);
        $select->order('access_permission.access desc');
        $select->where($where);
        $select->limit(1);

        $prepared = $this->sql->prepareStatementForSqlObject($select);
        $result = $this->returnResult($prepared->execute());

        return $result->current();
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getByName(string $name)
    {
        $tableName = $this->getTableName();
        $where = new Where();
        $where->equalTo('user_name', $name);

        $select = $this->sql->select();
        $select->columns([$select::SQL_STAR], false);
        $on = "access_permission.user_id = {$tableName}.id";
        $select->join("access_permission", $on, ['higher_access' => 'access'], $select::JOIN_LEFT);
        $select->order('access_permission.access desc');
        $select->where($where);
        $select->limit(1);

        $prepared = $this->sql->prepareStatementForSqlObject($select);
        $result = $this->returnResult($prepared->execute());

        return $result->current();
    }

    /**
     * @param UserEntity $user
     * @return void
     */
    public function updateAuthKey(UserEntity $user)
    {
        $where = new Where();
        $where->equalTo('id', $user->getId());
        $update = $this->sql->update();
        $update->where($where)->set(['auth_key' => $user->getAuthKey()]);

        $prepared = $this->sql->prepareStatementForSqlObject($update);
        $this->returnResult($prepared->execute());
    }

    /**
     * @param UserEntity $user
     * @return void
     */
    public function updatePasswordHash(UserEntity $user)
    {
        $where = new Where();
        $where->equalTo('id', $user->getId());
        $update = $this->sql->update();
        $update->where($where)->set(['password_hash' => $user->getPasswordHash()]);

        $prepared = $this->sql->prepareStatementForSqlObject($update);
        $this->returnResult($prepared->execute());
    }

    /**
     * @param string $email
     * @return mixed
     */
    public function getUserRestorePasswordByEmail(string $email)
    {
        $tableName = $this->getTableName();
        $this->objectPrototype = UserRestorePassword::class;

        $where = new Where();
        $where->equalTo('user_email', $email);

        $select = $this->sql->select();
        $select->columns([
            'id' => 'user.id',
            'user_email' => 'user.user_email',
            'user_name' => 'user.user_name',
            'access' => 'user.access',
            'restore_id' => 'restore_password.id',
            'created' => 'restore_password.created',
            'updated' => 'restore_password.updated',
            'secure_code' => 'restore_password.secure_code',
            'higher_access' => 'access_permission.access'
        ], false);
        $select->where($where);
        $select->join("restore_password", "restore_password.user_id = {$tableName}.id", [], $select::JOIN_LEFT);
        $select->join("access_permission", "access_permission.user_id = {$tableName}.id", [], $select::JOIN_LEFT);
        $select->order('access_permission.access desc');
        $select->limit(1);

        $prepared = $this->sql->prepareStatementForSqlObject($select);
        $result = $this->returnResult($prepared->execute());

        $this->objectPrototype = UserEntity::class;

        return $result->current();
    }
}