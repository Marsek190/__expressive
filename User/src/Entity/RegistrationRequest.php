<?php
declare(strict_types=1);

namespace User\Entity;


/**
 * Class RegistrationRequest
 * @package User\Entity
 */
class RegistrationRequest
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
     * @var string|null
     */
    protected $userName;
    /**
     * @var string|null
     */
    protected $confirmPassword;

    /**
     * @return string
     */
    public function getUserName(): ?string
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     * @return RegistrationRequest
     */
    public function setUserName(string $userName): RegistrationRequest
    {
        $this->userName = $userName;
        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    /**
     * @param string $confirmPassword
     * @return RegistrationRequest
     */
    public function setConfirmPassword(string $confirmPassword): RegistrationRequest
    {
        $this->confirmPassword = $confirmPassword;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return RegistrationRequest
     */
    public function setEmail(?string $email): RegistrationRequest
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
     * @return RegistrationRequest
     */
    public function setPassword(?string $password): RegistrationRequest
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
     * @return RegistrationRequest
     */
    public function setRememberMe(?bool $rememberMe): RegistrationRequest
    {
        $this->rememberMe = $rememberMe;
        return $this;
    }
}