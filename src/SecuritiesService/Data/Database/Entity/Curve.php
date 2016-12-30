<?php
namespace SecuritiesService\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="curves", indexes={@ORM\Index(name="date_indx", columns={"calculation_date"})})
 */
class Curve extends Entity
{
    /** @ORM\Column(type="string") */
    public $type;
    /** @ORM\Column(type="date") */
    public $calculationDate;
    /** @ORM\Column(type="text") */
    public $dataPoints;
}
