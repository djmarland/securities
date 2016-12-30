<?php
namespace SecuritiesService\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="yield_curves")})
 */
class YieldCurve extends Entity
{
    /** @ORM\Column(type="integer") */
    public $year;
    /** @ORM\Column(type="string") */
    public $type;
    /** @ORM\Column(type="text",length=5000) */
    public $dataPoints;
    /** @ORM\ManyToOne(targetEntity="Currency") */
    public $currency;
    /** @ORM\ManyToOne(targetEntity="ParentGroup") */
    public $parentGroup;
}
