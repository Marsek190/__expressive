<?php
declare(strict_types=1);

namespace User\Entity\Mail;


/**
 * Interface EmailRendererInterface
 * @package User\Entity\Mail
 */
interface EmailRendererInterface
{
    /**
     * Render email with template variables and subject.
     * Returning rendered html string.
     *
     * @param SenderConfiguration $configuration
     * @return string
     */
    public function render(SenderConfiguration $configuration): string;
}