<?php
declare(strict_types=1);

namespace User\Entity;


use Core\Entity\User\User;

/**
 * Class UserRestorePassword
 * @package User\Entity
 */
class UserRestorePassword extends User
{
    /**
     * @var int|null
     */
    protected $restore_id;
    /**
     * @var int|null
     */
    protected $created;
    /**
     * @var int|null
     */
    protected $updated;
    /**
     * @var string|null
     */
    protected $secure_code;
    /**
     * @var string|null
     */
    protected $resettingLink;

    /**
     * @return int|null
     */
    public function getRestoreId(): ?int
    {
        return $this->restore_id;
    }

    /**
     * @param int|null $restore_id
     * @return UserRestorePassword
     */
    public function setRestoreId(?int $restore_id): UserRestorePassword
    {
        $this->restore_id = $restore_id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCreated(): ?int
    {
        return $this->created;
    }

    /**
     * @param int|null $created
     * @return UserRestorePassword
     */
    public function setCreated(?int $created): UserRestorePassword
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getUpdated(): ?int
    {
        return $this->updated;
    }

    /**
     * @param int|null $updated
     * @return UserRestorePassword
     */
    public function setUpdated(?int $updated): UserRestorePassword
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSecureCode(): ?string
    {
        return $this->secure_code;
    }

    /**
     * @param string|null $secure_code
     * @return UserRestorePassword
     */
    public function setSecureCode(?string $secure_code): UserRestorePassword
    {
        $this->secure_code = $secure_code;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getResettingLink(): ?string
    {
        return $this->resettingLink;
    }

    /**
     * @param string|null $resettingLink
     * @return UserRestorePassword
     */
    public function setResettingLink(?string $resettingLink): UserRestorePassword
    {
        $this->resettingLink = $resettingLink;
        return $this;
    }
}