<?php
namespace SecuritiesService\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="indices")})
 */
class Index extends Entity
{
    /** @ORM\Column(type="string", length=255) */
    public $name;
}
