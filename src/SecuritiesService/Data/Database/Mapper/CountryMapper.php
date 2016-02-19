<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\Country;
use SecuritiesService\Domain\ValueObject\UUID;

class CountryMapper extends Mapper
{
    public function getDomainModel(array $item): Entity
    {
        $id = new UUID($item['id']);
        $currency = new Country(
            $id,
            $item['name']
        );
        return $currency;
    }
}
