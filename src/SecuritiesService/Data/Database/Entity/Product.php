<?php
namespace SecuritiesService\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="products")})
 */
class Product extends Entity
{
    /** @ORM\Column(type="integer", length=2) */
    private $number;

    /** Getters/Setters */
    public function getNumber()
    {
        return $this->number;
    }

    public function setNumber($number)
    {
        $this->number = $number;
    }

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