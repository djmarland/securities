<?php

namespace AppBundle\Domain\ValueObject;

use AppBundle\Domain\Exception\ValidationException;

/**
 * Class Address
 * For handling addresses
 */
class Password
{

    public function __construct(
        $plainText
    ) {
        $this->validate($plainText);

        // generate digest, and never store the original password
        $digest = password_hash($plainText, PASSWORD_DEFAULT);
        $this->digest = new PasswordDigest($digest);
        unset($plainText); // even remove it from memory
    }

    /**
     * @var string
     */
    private $digest;

    public function getDigest()
    {
        return $this->digest;
    }

    /**
     * @param $plainText
     * @return bool
     * @throws ValidationException
     */
    protected function validate($plainText)
    {
        if (strlen($plainText) < 6) {
            throw new ValidationException('Password must be at least 6 characters long');
        }
        if (in_array($plainText, [
            '123456',
            'password'
        ])) {
            throw new ValidationException('You must choose a more secure password');
        }
        return true;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getDigest();
    }
}
