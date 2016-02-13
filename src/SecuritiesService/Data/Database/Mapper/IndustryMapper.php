<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\Industry;
use SecuritiesService\Domain\ValueObject\ID;

class IndustryMapper extends Mapper
{
    public function getDomainModel(array $item): Entity
    {
        $id = new ID($item['id']);
        $currency = new Industry(
            $id,
            $item['name']
        );
        return $currency;
    }
}
