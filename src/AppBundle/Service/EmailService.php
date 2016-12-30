<?php

namespace AppBundle\Service;

use SecuritiesService\Domain\ValueObject\Email;
use Swift_Mailer;
use Swift_Message;

class EmailService
{

    private $mailer;

    private $fromAddress;

    private $fromName;

    public function __construct(
        Swift_Mailer $mailer,
        string $fromAddress,
        string $fromName
    ) {
        $this->mailer = $mailer;
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
    }

    public function send(
        Email $to,
        string $subject,
        string $body
    ) {
        $message = Swift_Message::newInstance()
            ->setFrom($this->fromAddress, $this->fromName)
            ->setSubject($subject)
            ->setTo((string) $to)
            ->setBody($body, 'text/html');

        return $this->mailer->send($message);
    }
}
