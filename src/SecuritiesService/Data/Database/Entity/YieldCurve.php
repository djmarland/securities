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
    private $year;

    /** Getters/Setters */
    public function getYear()
    {
        return $this->year;
    }

    public function setYear($year)
    {
        $this->year = $year;
    }

    /** @ORM\Column(type="string") */
    private $type;

    /** Getters/Setters */
    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    /** @ORM\Column(type="string") */
    private $dataPoints;

    /** Getters/Setters */
    public function getDataPoints()
    {
        return $this->dataPoints;
    }

    public function setDataPoints($dataPoints)
    {
        $this->dataPoints = $dataPoints;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Currency")
     */
    private $currency;

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @ORM\ManyToOne(targetEntity="ParentGroup")
     */
    private $parentGroup;

    public function getParentGroup()
    {
        return $this->parentGroup;
    }

    public function setParentGroup($parentGroup)
    {
        $this->parentGroup = $parentGroup;
    }
}