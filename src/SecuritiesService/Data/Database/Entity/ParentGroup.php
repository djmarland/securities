<?php
namespace SecuritiesService\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="parent_groups")})
 */
class ParentGroup extends Entity
{
    /** @ORM\Column(type="string", length=255) */
    public $name;
    /** @ORM\ManyToOne(targetEntity="Sector") */
    public $sector;
}
