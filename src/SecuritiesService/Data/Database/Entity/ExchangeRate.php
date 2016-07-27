<?php
namespace SecuritiesService\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="exchange_rates")})
 */
class ExchangeRate extends Entity
{
    /** @ORM\Column(type="float", nullable=false) */
    private $rate;

    /** @ORM\Column(type="date", nullable=false) */
    private $date;

    /** @ORM\ManyToOne(targetEntity="Currency") */
    private $currency;

    /** Getters/Setters */
    public function getRate()
    {
        return $this->rate;
    }

    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }
}
