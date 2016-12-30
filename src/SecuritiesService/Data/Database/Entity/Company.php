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
    public $name;
    /** @ORM\Column(type="string", length=255, nullable=true) */
    public $marketCode;
    /** @ORM\ManyToOne(targetEntity="Country") */
    public $country;
    /** @ORM\ManyToOne(targetEntity="ParentGroup") */
    public $parentGroup;
}
