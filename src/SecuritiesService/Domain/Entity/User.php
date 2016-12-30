<?php
namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\ValueObject\Email;
use SecuritiesService\Domain\ValueObject\UUID;
use SecuritiesService\Domain\ValueObject\PasswordDigest;

class User extends Entity
{
    const KEY_PREFIX = 'U';

    private $name;
    private $email;
    private $passwordDigest;
    private $passwordExpired = false;
    private $isActive = true;
    private $isAdmin = false;

    public function __construct(
        UUID $id,
        string $name,
        Email $email,
        PasswordDigest $passwordDigest,
        bool $passwordExpired = false,
        bool $isActive = true,
        bool $isAdmin = false
    ) {
        parent::__construct($id);
        $this->name = $name;
        $this->email = $email;
        $this->passwordExpired = $passwordExpired;
        $this->passwordDigest = $passwordDigest;
        $this->isActive = $isActive;
        $this->isAdmin = $isAdmin;
    }

    public function getName(): string
    {
        return $this->name;
    }
//    public function setName(string $name): void
//    {
//        if ($name != $this->name) {
//            // @todo - validate not empty
//            $this->name = $name;
//            $this->updated();
//        }
//    }

    public function getEmail(): Email
    {
        return $this->email;
    }
//    public function setEmail(Email $email): void
//    {
//        if ((string) $email != (string) $this->email) {
//            // @todo - validate not empty
//            // @todo - reset the user to unconfirmed
//            $this->email = $email;
//            $this->updated();
//        }
//    }

    public function getPasswordDigest(): PasswordDigest
    {
        return $this->passwordDigest;
    }

//    public function setPasswordDigest($newPassword): void
//    {
//        $password = new Password($newPassword);
//        $this->passwordDigest = $password->getDigest();
//    }
    public function passwordMatches($match): bool
    {
        return $this->getPasswordDigest()->matches($match);
    }

    public function passwordHasExpired(): bool
    {
        return $this->passwordExpired;
    }
//    public function expirePassword()
//    {
//        $this->passwordExpired = true;
//    }
//    public function renewPassword()
//    {
//        $this->passwordExpired = false;
//    }
    public function isActive(): bool
    {
        return $this->isActive;
    }
//    public function activate()
//    {
//        $this->isActive = true;
//        $this->updated();
//    }
//    public function deactivate()
//    {
//        $this->isActive = false;
//        $this->updated();
//    }
    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }
//    public function makeAdmin(): void
//    {
//        $this->isAdmin = true;
//        $this->updated();
//    }
//    public function revokeAdmin(): void
//    {
//        $this->isAdmin = false;
//        $this->updated();
//    }
}
