<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\ValueObject\ID;

class YieldCurve extends Entity
{
    public function __construct(
        ID $id,
        int $year,
        Currency $currency,
        array $dataPoints
    ) {
        parent::__construct($id);

        $this->year = $year;
        $this->currency = $currency;
        $this->dataPoints = $dataPoints;
    }

    private $year;

    public function getYear()
    {
        return $this->year;
    }

    private $currency;

    public function getCurrency()
    {
        return $this->currency;
    }

    private $dataPoints;

    public function getDataPoints()
    {
        return $this->dataPoints;
    }
}