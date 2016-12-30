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
    public $number;
    /** @ORM\Column(type="string", length=255) */
    public $name;
}
