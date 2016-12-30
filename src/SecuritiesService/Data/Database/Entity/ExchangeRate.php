<?php
namespace SecuritiesService\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="exchange_rates",indexes={@ORM\Index(name="exchange_rates_date_idx", columns={"date"})})
 */
class ExchangeRate extends Entity
{
    /** @ORM\Column(type="float", nullable=false) */
    public $rate;
    /** @ORM\Column(type="date", nullable=false) */
    public $date;
    /** @ORM\ManyToOne(targetEntity="Currency") */
    public $currency;
}
