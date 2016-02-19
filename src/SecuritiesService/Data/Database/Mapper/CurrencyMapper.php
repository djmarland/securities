<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\Currency;
use SecuritiesService\Domain\ValueObject\UUID;

class CurrencyMapper extends Mapper
{
    public function getDomainModel(array $item): Entity
    {
        $id = new UUID($item['id']);
        $currency = new Currency(
            $id,
            $item['code']
        );
        return $currency;
    }
}
