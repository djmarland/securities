<?php
namespace SecuritiesService\Data\Database\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="users")
 */
class User extends Entity
{
    /** @ORM\Column(type="string", length=255) */
    private $name = '';

    /** @ORM\Column(type="string", length=255) */
    private $email;

    /** @ORM\Column(type="string", length=255) */
    private $passwordDigest;

    /** @ORM\Column(type="boolean") */
    private $isAdmin = false;

    /** @ORM\Column(type="boolean") */
    private $isActive = true;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $resetTokenUsername;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $resetTokenDigest;

    /** @ORM\Column(type="datetime", nullable=true) */
    private $resetTokenExpiry;


    /** Getters/Setters */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPasswordDigest()
    {
        return $this->passwordDigest;
    }

    public function setPasswordDigest($passwordDigest)
    {
        $this->passwordDigest = $passwordDigest;
    }

    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    public function setIsAdmin($bool)
    {
        $this->isAdmin = $bool;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setIsActive($bool)
    {
        $this->isActive = $bool;
    }

    public function getResetTokenUsername()
    {
        return $this->resetTokenUsername;
    }

    public function setResetTokenUsername($token)
    {
        $this->resetTokenUsername = $token;
    }

    public function getResetTokenDigest()
    {
        return $this->resetTokenDigest;
    }

    public function setResetTokenDigest($passwordDigest)
    {
        $this->resetTokenDigest = $passwordDigest;
    }

    public function getResetTokenExpiry()
    {
        return $this->resetTokenExpiry;
    }
    
    public function setResetTokenExpiry($datetime)
    {
        $this->resetTokenExpiry = $datetime;
    }
}