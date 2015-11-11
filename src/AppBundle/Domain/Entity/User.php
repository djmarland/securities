<?php

namespace AppBundle\Domain\Entity;

use AppBundle\Domain\ValueObject\Email;
use AppBundle\Domain\ValueObject\ID;
use AppBundle\Domain\ValueObject\Password;
use AppBundle\Domain\ValueObject\PasswordDigest;
use DateTime;

/**
 * Class User
 * For describe users of the system
 */
class User extends Entity
{
    const KEY_PREFIX = 'U';

    /**
     * @param ID $id
     * @param DateTime $createdAt
     * @param DateTime $updatedAt
     * @param $name
     * @param Email $email
     * @param $passwordDigest
     * @param bool $passwordExpired
     * @param bool $isActive
     * @param bool $isAdmin
     */
    public function __construct(
        ID $id,
        DateTime $createdAt,
        DateTime $updatedAt,
        $name,
        Email $email,
        $passwordDigest,
        $passwordExpired = false,
        $isActive = true,
        $isAdmin = false
    ) {
        parent::__construct(
            $id,
            $createdAt,
            $updatedAt
        );

        $this->name = $name;
        $this->email = $email;
        $this->passwordExpired = $passwordExpired;
        $this->passwordDigest = $passwordDigest;
        $this->isActive = $isActive;
        $this->isAdmin = $isAdmin;
    }

    /**
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        if ($name != $this->name) {
            // @todo - validate not empty
            $this->name = $name;
            $this->updated();
        }
    }

    /**
     * @var string
     */
    private $email;

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(Email $email)
    {
        if ((string)$email != (string)$this->email) {
            // @todo - validate not empty

            // @todo - reset the user to unconfirmed
            $this->email = $email;
            $this->updated();
        }
    }

    /**
     * @var PasswordDigest
     */
    private $passwordDigest;

    /**
     * @return PasswordDigest
     */
    public function getPasswordDigest()
    {
        return $this->passwordDigest;
    }

    /**
     * @param $newPassword
     */
    public function setPasswordDigest($newPassword)
    {
        $password = new Password($newPassword);
        $this->passwordDigest = $password->getDigest();
    }

    public function passwordMatches($match)
    {
        return $this->getPasswordDigest()->matches($match);
    }

    /**
     * @var boolean
     */
    private $passwordExpired = false;

    /**
     * @return boolean
     */
    public function passwordHasExpired()
    {
        return $this->passwordExpired;
    }

    public function expirePassword()
    {
        $this->passwordExpired = true;
    }

    public function renewPassword()
    {
        $this->passwordExpired = false;
    }

    /**
     * @var boolean
     */
    private $isActive = true;

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->isActive;
    }

    public function activate()
    {
        $this->isActive = true;
        $this->updated();
    }

    public function deactivate()
    {
        $this->isActive = false;
        $this->updated();
    }

    /**
     * @var boolean
     */
    private $isAdmin = false;

    /**
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->isAdmin;
    }

    public function makeAdmin()
    {
        $this->isAdmin = true;
        $this->updated();
    }

    public function revokeAdmin()
    {
        $this->isAdmin = false;
        $this->updated();
    }
}