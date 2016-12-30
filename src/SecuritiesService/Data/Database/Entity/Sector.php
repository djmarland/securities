<?php
namespace SecuritiesService\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="sectors")})
 */
class Sector extends Entity
{
    /** @ORM\Column(type="string", length=255) */
    public $name;
    /** @ORM\ManyToOne(targetEntity="Industry") */
    public $industry;
}
