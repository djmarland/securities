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
    /** @ORM\Column(type="string") */
    private $type;
    /** @ORM\Column(type="text",length=5000) */
    private $dataPoints;
    /** @ORM\ManyToOne(targetEntity="Currency") */
    private $currency;
    /** @ORM\ManyToOne(targetEntity="ParentGroup") */
    private $parentGroup;

    /** Getters/Setters */
    public function getYear()
    {
        return $this->year;
    }

    public function setYear($year)
    {
        $this->year = $year;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getDataPoints()
    {
        return $this->dataPoints;
    }

    public function setDataPoints($dataPoints)
    {
        $this->dataPoints = $dataPoints;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function getParentGroup()
    {
        return $this->parentGroup;
    }

    public function setParentGroup($parentGroup)
    {
        $this->parentGroup = $parentGroup;
    }
}
