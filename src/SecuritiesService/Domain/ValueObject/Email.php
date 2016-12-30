<?php
namespace SecuritiesService\Domain\ValueObject;

use SecuritiesService\Domain\Exception\ValidationException;

class Email
{
    private $email;

    public function __construct(
        string $email
    ) {
        $email = trim(strtolower($email));
        $this->validate($email);
        $this->email = $email;
    }

    public function __toString()
    {
        return $this->getEmail();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    private function validate(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException('E-mail address is not valid');
        }
        return true;
    }
}
