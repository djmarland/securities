<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Data\Database\Entity\Entity as EntityOrm;
use SecuritiesService\Domain\Entity\YieldCurve;
use SecuritiesService\Domain\ValueObject\ID;

class YieldCurveMapper extends Mapper
{
    public function getDomainModel(array $item): Entity
    {
        $id = new ID($item['id']);
        $currency = $this->mapperFactory->createCurrency()->getDomainModel($item['currency']);
        $dataPoints = (array) json_decode($item['dataPoints']);
        $model = new YieldCurve(
            $id,
            $item['year'],
            $currency,
            $dataPoints
        );
        return $model;
    }
}
