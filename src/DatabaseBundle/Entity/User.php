<?php
namespace DatabaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity
* @ORM\HasLifecycleCallbacks()
* @ORM\Table(name="users")
*/
class User extends Entity
{
    /** @ORM\Column(type="string", length=255) */
    private $name;

    /** @ORM\Column(type="string", length=255) */
    private $email;

    /** @ORM\Column(type="string", length=255) */
    private $password_digest;

    /** @ORM\Column(type="boolean") */
    private $is_admin;

    /** @ORM\Column(type="boolean") */
    private $is_active;

    /** @ORM\Column(type="boolean") */
    private $password_expired;

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
        return $this->password_digest;
    }

    public function setPasswordDigest($passwordDigest)
    {
        $this->password_digest = $passwordDigest;
    }

    public function getIsAdmin()
    {
        return $this->is_admin;
    }

    public function setIsAdmin($bool)
    {
        $this->is_admin = $bool;
    }

    public function getIsActive()
    {
        return $this->is_active;
    }

    public function setIsActive($bool)
    {
        $this->is_active = $bool;
    }

    public function getPasswordExpired()
    {
        return $this->password_expired;
    }

    public function setPasswordExpired($bool)
    {
        $this->password_expired = $bool;
    }
}
