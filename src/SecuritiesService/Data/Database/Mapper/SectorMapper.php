<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\Sector;
use SecuritiesService\Domain\ValueObject\UUID;

class SectorMapper extends Mapper
{
    public function getDomainModel(array $item): Entity
    {
        $id = new UUID($item['id']);
        $industry = null;

        if (isset($item['industry'])) {
            $industry = $this->mapperFactory->createIndustry()->getDomainModel($item['industry']);
        }

        $currency = new Sector(
            $id,
            $item['name'],
            $industry
        );
        return $currency;
    }
}
