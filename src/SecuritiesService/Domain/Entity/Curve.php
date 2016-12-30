<?php

namespace SecuritiesService\Domain\Entity;

use DateTimeInterface;
use SecuritiesService\Domain\ValueObject\CurvePoints;
use SecuritiesService\Domain\ValueObject\UUID;

class Curve extends Entity
{
    private $type;
    private $calculationDate;
    private $curvePoints;

    public function __construct(
        UUID $id,
        string $type,
        DateTimeInterface $calculationDate,
        CurvePoints $curvePoints
    ) {
        parent::__construct($id);

        $this->type = $type;
        $this->calculationDate = $calculationDate;
        $this->curvePoints = $curvePoints;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCalculationDate(): DateTimeInterface
    {
        return $this->calculationDate;
    }

    public function getPoints(): CurvePoints
    {
        return $this->curvePoints;
    }
}
