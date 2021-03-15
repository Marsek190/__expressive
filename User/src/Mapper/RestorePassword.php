<?php
declare(strict_types=1);

namespace User\Mapper;


use Core\Entity\User\RestorePassword as RestorePasswordEntity;
use Zend\Db\Sql\Where;

/**
 * Class RestorePassword
 * @package User\Mapper
 */
class RestorePassword extends \Core\Mapper\User\RestorePassword
{
    /**
     * @param int $userId
     * @return mixed
     */
    public function getByUserId(int $userId)
    {
        $where = new Where();
        $where->equalTo('user_id', $userId);

        $select = $this->sql->select();
        $select->columns([$select::SQL_STAR], false);
        $select->where($where);
        $select->limit(1);

        $prepared = $this->sql->prepareStatementForSqlObject($select);
        $result = $this->returnResult($prepared->execute());

        return $result->current();
    }

    /**
     * @param RestorePasswordEntity $restorePassword
     * @return void
     */
    public function updateSecureCodeAndUpdatedFields(RestorePasswordEntity $restorePassword)
    {
        $where = new Where();
        $where->equalTo('id', $restorePassword->getId());
        $updatedRows = [
            'updated' => $restorePassword->getUpdated(),
            'secure_code' => $restorePassword->getSecureCode()
        ];

        $update = $this->sql->update();
        $update->where($where)->set($updatedRows);

        $prepared = $this->sql->prepareStatementForSqlObject($update);
        $this->returnResult($prepared->execute());
    }
}