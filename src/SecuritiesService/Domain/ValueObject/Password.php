<?php

namespace SecuritiesService\Domain\ValueObject;

use SecuritiesService\Domain\Exception\ValidationException;

class Password
{
    private $digest;

    public function __construct(
        string $plainText
    ) {
        $this->validate($plainText);

        // generate digest, and never store the original password
        $digest = password_hash($plainText, PASSWORD_DEFAULT);
        $this->digest = new PasswordDigest($digest);
        unset($plainText); // even remove it from memory
    }

    public function __toString(): string
    {
        return (string) $this->getDigest();
    }

    public function getDigest(): PasswordDigest
    {
        return $this->digest;
    }

    private function validate(string $plainText): bool
    {
        if (strlen($plainText) < 6) {
            throw new ValidationException('Password must be at least 6 characters long');
        }
        if (in_array($plainText, [
            '123456',
            'password',
        ])) {
            throw new ValidationException('You must choose a more secure password');
        }
        return true;
    }
}
