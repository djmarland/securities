<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Data\Database\Entity\Entity as EntityOrm;
use SecuritiesService\Domain\Entity\YieldCurve;
use SecuritiesService\Domain\ValueObject\ID;

class YieldCurveMapper extends Mapper
{
    public function getDomainModel(EntityOrm $item): Entity
    {
        $id = new ID($item->getId());
        $currency = $this->mapperFactory->getDomainModel($item->getCurrency());
        $dataPoints = (array) json_decode($item->getDataPoints());
        $model = new YieldCurve(
            $id,
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
            $item->getYear(),
            $currency,
            $dataPoints
        );
        return $model;
    }
}
