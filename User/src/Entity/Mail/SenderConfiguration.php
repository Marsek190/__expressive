<?php
declare(strict_types=1);

namespace User\Entity\Mail;


/**
 * Class SenderConfiguration
 * @package User\Entity\Mail
 */
final class SenderConfiguration
{
    private string $from;

    private array $recipients = [];

    private string $subject;

    private array $templateVariables = [];

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @param string $from
     * @return SenderConfiguration
     */
    public function setFrom(string $from): SenderConfiguration
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return array
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @param array $recipients
     * @return SenderConfiguration
     */
    public function setRecipients(array $recipients): SenderConfiguration
    {
        $this->recipients = $recipients;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return SenderConfiguration
     */
    public function setSubject(string $subject): SenderConfiguration
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return array
     */
    public function getTemplateVariables()
    {
        return $this->templateVariables;
    }

    /**
     * @param array $templateVariables
     * @return SenderConfiguration
     */
    public function setTemplateVariables(array $templateVariables): SenderConfiguration
    {
        $this->templateVariables = $templateVariables;
        return $this;
    }
}