<?php

namespace AppBundle\Domain\ValueObject;

use AppBundle\Domain\Exception\ValidationException;

/**
 * Class Address
 * For handling addresses
 */
class Email
{

    public function __construct(
        $email
    ) {
        $email = trim(strtolower($email));
        $this->validate($email);
        $this->email = $email;
    }

    /**
     * @var string
     */
    private $email;

    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $email
     * @return bool
     * @throws ValidationException
     */
    protected function validate($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException('E-mail address is not valid');
        }
        return true;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getEmail();
    }
}
