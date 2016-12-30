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
    public $name = '';
    /** @ORM\Column(type="string", length=255) */
    public $email;
    /** @ORM\Column(type="string", length=255) */
    public $passwordDigest;
    /** @ORM\Column(type="boolean") */
    public $isAdmin = false;
    /** @ORM\Column(type="boolean") */
    public $isActive = true;
    /** @ORM\Column(type="string", length=255, nullable=true) */
    public $resetTokenUsername;
    /** @ORM\Column(type="string", length=255, nullable=true) */
    public $resetTokenDigest;
    /** @ORM\Column(type="datetime", nullable=true) */
    public $resetTokenExpiry;
}
