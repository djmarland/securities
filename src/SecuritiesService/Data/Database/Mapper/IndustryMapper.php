<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\Industry;
use SecuritiesService\Domain\ValueObject\UUID;

class IndustryMapper extends Mapper
{
    public function getDomainModel(array $item): Entity
    {
        $id = new UUID($item['id']);
        $currency = new Industry(
            $id,
            $item['name']
        );
        return $currency;
    }
}
