<?php
declare(strict_types=1);

namespace User\Entity\Mail;


use Core\Entity\User\AbstractRootEntity;

/**
 * Interface SenderInterface
 * @package User\Entity\Mail
 */
interface SenderInterface
{
    /**
     * @param AbstractRootEntity $user
     * @return void
     */
    public function send(AbstractRootEntity $user): void;
}