<?php
declare(strict_types=1);

namespace User\Entity;


/**
 * Class AuthRequest
 * @package User\Entity
 */
class AuthRequest
{
    /**
     * @var string|null
     */
    protected $email;
    /**
     * @var string|null
     */
    protected $password;
    /**
     * @var bool|null
     */
    protected $rememberMe;

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return AuthRequest
     */
    public function setEmail(?string $email): AuthRequest
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return AuthRequest
     */
    public function setPassword(?string $password): AuthRequest
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function isRememberMe(): ?bool
    {
        return $this->rememberMe;
    }

    /**
     * @param bool|null $rememberMe
     * @return AuthRequest
     */
    public function setRememberMe(?bool $rememberMe): AuthRequest
    {
        $this->rememberMe = $rememberMe;
        return $this;
    }
}