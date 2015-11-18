<?php
namespace DatabaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="security_types")})
 */
class SecurityType extends Entity
{
    /** @ORM\Column(type="string", length=255) */
    private $name;

    /** Getters/Setters */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}