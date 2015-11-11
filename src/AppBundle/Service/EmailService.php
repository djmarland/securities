<?php

namespace AppBundle\Service;

use AppBundle\Domain\ValueObject\Email;
use Swift_Mailer;
use Swift_Message;

/**
 * Default factory setup
 * Class ServiceFactory
 * @package App\Infrastructure
 */
class EmailService
{

    private $mailer;

    private $fromAddress;

    public function __construct(
        Swift_Mailer $mailer,
        $fromAddress
    ) {
        $this->mailer = $mailer;
        $this->fromAddress = $fromAddress;
    }


    /**
     * @param $to
     * @param $fromName
     * @param $subject
     * @param $body
     * @return bool
     * @throws \AppBundle\Domain\Exception\DataNotSetException
     */
    public function send(
        Email $to,
        $fromName,
        $subject,
        $body
    ) {
        $message = Swift_Message::newInstance()
            ->setFrom($this->fromAddress, $fromName)
            ->setSubject($subject)
            ->setTo((string) $to)
            ->setBody($body, 'text/html');

        return $this->mailer->send($message);
    }

}
