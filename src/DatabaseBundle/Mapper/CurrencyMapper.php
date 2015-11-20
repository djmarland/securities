<?php

namespace DatabaseBundle\Mapper;

use AppBundle\Domain\Entity\Entity;
use DatabaseBundle\Entity\Entity as EntityOrm;
use AppBundle\Domain\Entity\Currency;
use AppBundle\Domain\ValueObject\ID;

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
