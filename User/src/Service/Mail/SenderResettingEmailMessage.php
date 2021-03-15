<?php
declare(strict_types=1);

namespace User\Service\Mail;


use Core\Entity\User\AbstractRootEntity;
use User\Entity\Mail\SenderConfiguration;
use User\Entity\Mail\SenderInterface;
use User\Entity\UserRestorePassword;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;

final class SenderResettingEmailMessage implements SenderInterface
{
    protected TransportInterface $transport;

    protected RenderedEmail $rendererEmail;

    protected SenderConfiguration $senderConfiguration;

    /**
     * @param TransportInterface $transport
     * @param RenderedEmail $rendererEmail
     * @param SenderConfiguration $senderConfiguration
     */
    public function __construct(
        TransportInterface $transport,
        RenderedEmail $rendererEmail,
        SenderConfiguration $senderConfiguration)
    {
        $this->transport = $transport;
        $this->rendererEmail = $rendererEmail;
        $this->senderConfiguration = $senderConfiguration;
    }

    /**
     * @param AbstractRootEntity $user
     * @return void
     */
    public function send(AbstractRootEntity $user): void
    {
        /** @var UserRestorePassword $user */
        $body = $this->rendererEmail->render($this->senderConfiguration->setTemplateVariables([
            'resettingLink' =>$user->getResettingLink()
        ]));
        $message = (new Message())
            ->setFrom($this->senderConfiguration->getFrom())
            ->addTo($user->getUserEmail(), $user->getUserName())
            ->setSubject($this->senderConfiguration->getSubject())
            ->setBody($body);

        $this->transport->send($message);
    }
}