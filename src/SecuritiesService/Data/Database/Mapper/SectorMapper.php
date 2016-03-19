<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\Null\NullSector;
use SecuritiesService\Domain\Entity\Sector;
use SecuritiesService\Domain\ValueObject\UUID;

class SectorMapper extends Mapper
{
    public function getDomainModel(array $item = null): Entity
    {
        if (!$item) {
            // a sector was called, but the result was null
            return new NullSector();
        }

        $id = new UUID($item['id']);
        $industry = null;
        
        // uses array_key_exists so we can associate a NullObject with it if needed
        if (array_key_exists('industry', $item)) {
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
