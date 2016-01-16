<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Data\Database\Entity\Entity as EntityOrm;
use SecuritiesService\Domain\Entity\Currency;
use SecuritiesService\Domain\ValueObject\ID;

class CurrencyMapper extends Mapper
{
    public function getDomainModel(EntityOrm $item): Entity
    {
        $id = new ID($item->getId());
        $currency = new Currency(
            $id,
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
            $item->getCode()
        );
        return $currency;
    }
}
