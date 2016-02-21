<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\ValueObject\UUID;

class YieldCurve extends Entity
{

    private $year;
    private $currency;
    private $dataPoints;

    public function __construct(
        UUID $id,
        int $year,
        Currency $currency,
        array $dataPoints
    ) {
        parent::__construct($id);

        $this->year = $year;
        $this->currency = $currency;
        $this->dataPoints = $dataPoints;
    }

    public function getYear()
    {
        return $this->year;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getDataPoints()
    {
        return $this->dataPoints;
    }
}
