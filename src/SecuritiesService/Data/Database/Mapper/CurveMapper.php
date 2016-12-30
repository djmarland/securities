<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Curve;
use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\ValueObject\CurvePoints;
use SecuritiesService\Domain\ValueObject\UUID;

class CurveMapper extends Mapper
{
    public function getDomainModel(array $item): Entity
    {
        $id = new UUID($item['id']);
        $dataPoints = new CurvePoints((array) json_decode($item['dataPoints'], true));
        $model = new Curve(
            $id,
            $item['type'],
            $item['calculationDate'],
            $dataPoints
        );
        return $model;
    }
}
