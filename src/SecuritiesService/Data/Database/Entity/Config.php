<?php
namespace SecuritiesService\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="config")})
 */
class Config extends Entity
{
    /** @ORM\Column(type="json_array") */
    public $settings = [];
    /** @ORM\Column(type="json_array") */
    public $features = [];
}
