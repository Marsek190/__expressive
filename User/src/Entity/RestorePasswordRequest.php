<?php
declare(strict_types=1);

namespace User\Entity;


/**
 * Class RestorePasswordRequest
 * @package User\Entity
 */
class RestorePasswordRequest
{
    /**
     * @var string|null
     */
    protected $password;
    /**
     * @var string|null
     */
    protected $confirmPassword;
    /**
     * @var int|null
     */
    protected $userId;
    /**
     * @var string|null
     */
    protected $secureCode;

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return RestorePasswordRequest
     */
    public function setPassword(?string $password): RestorePasswordRequest
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    /**
     * @param string|null $confirmPassword
     * @return RestorePasswordRequest
     */
    public function setConfirmPassword(?string $confirmPassword): RestorePasswordRequest
    {
        $this->confirmPassword = $confirmPassword;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int|null $userId
     * @return RestorePasswordRequest
     */
    public function setUserId(?int $userId): RestorePasswordRequest
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSecureCode(): ?string
    {
        return $this->secureCode;
    }

    /**
     * @param string|null $secureCode
     * @return RestorePasswordRequest
     */
    public function setSecureCode(?string $secureCode): RestorePasswordRequest
    {
        $this->secureCode = $secureCode;
        return $this;
    }
}