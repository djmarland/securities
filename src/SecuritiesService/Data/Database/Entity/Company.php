<?php
namespace SecuritiesService\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="companies")})
 */
class Company extends Entity
{
    /** @ORM\Column(type="string", length=255) */
    private $name;
    /** @ORM\ManyToOne(targetEntity="Country") */
    private $country;
    /** @ORM\ManyToOne(targetEntity="ParentGroup") */
    private $parentGroup;

    /** Getters/Setters */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function getParentGroup()
    {
        return $this->parentGroup;
    }

    public function setParentGroup($parentGroup)
    {
        $this->parentGroup = $parentGroup;
    }
}
