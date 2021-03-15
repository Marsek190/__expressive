<?php
declare(strict_types=1);

namespace User\Entity;


use Core\Entity\User\AbstractRootEntity;

/**
 * Class UserRegistration
 * @package User\Entity
 */
class UserRegistration extends AbstractRootEntity
{
    /**
     * @var int|null
     */
    protected $id;
    /**
     * @var string|null
     */
    protected $auth_key;
    /**
     * @var string|null
     */
    protected $user_name;
    /**
     * @var string|null
     */
    protected $password_hash;
    /**
     * @var string|null
     */
    protected $user_email;
    /**
     * @var int|null
     */
    protected $act_time;
    /**
     * @var int|null
     */
    protected $reg_time;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return UserRegistration
     */
    public function setId(?int $id): UserRegistration
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAuthKey(): ?string
    {
        return $this->auth_key;
    }

    /**
     * @param string|null $auth_key
     * @return UserRegistration
     */
    public function setAuthKey(?string $auth_key): UserRegistration
    {
        $this->auth_key = $auth_key;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserName(): ?string
    {
        return $this->user_name;
    }

    /**
     * @param string|null $user_name
     * @return UserRegistration
     */
    public function setUserName(?string $user_name): UserRegistration
    {
        $this->user_name = $user_name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPasswordHash(): ?string
    {
        return $this->password_hash;
    }

    /**
     * @param string|null $password_hash
     * @return UserRegistration
     */
    public function setPasswordHash(?string $password_hash): UserRegistration
    {
        $this->password_hash = $password_hash;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserEmail(): ?string
    {
        return $this->user_email;
    }

    /**
     * @param string|null $user_email
     * @return UserRegistration
     */
    public function setUserEmail(?string $user_email): UserRegistration
    {
        $this->user_email = $user_email;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getActTime(): ?int
    {
        return $this->act_time;
    }

    /**
     * @param int|null $act_time
     * @return UserRegistration
     */
    public function setActTime(?int $act_time): UserRegistration
    {
        $this->act_time = $act_time;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getRegTime(): ?int
    {
        return $this->reg_time;
    }

    /**
     * @param int|null $reg_time
     * @return UserRegistration
     */
    public function setRegTime(?int $reg_time): UserRegistration
    {
        $this->reg_time = $reg_time;
        return $this;
    }
}