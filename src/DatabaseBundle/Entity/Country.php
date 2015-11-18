<?php
namespace DatabaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="countries")})
 */
class Country extends Entity
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

    /**
     * @ORM\ManyToOne(targetEntity="Region")
     */
    private $region;

    public function getRegion()
    {
        return $this->region;
    }

    public function setRegion($region)
    {
        $this->region = $region;
    }
}