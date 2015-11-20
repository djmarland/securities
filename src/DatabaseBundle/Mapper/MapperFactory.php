<?php

namespace DatabaseBundle\Mapper;

use AppBundle\Domain\Entity\Entity;
use DatabaseBundle\Entity\Entity as EntityOrm;
use DatabaseBundle\Entity\Security as SecurityOrm;
use DatabaseBundle\Entity\Currency as CurrencyOrm;

/**
 * Factory to create mappers as needed
 */
class MapperFactory
{

    public function __construct()
    {
    }

    public function getMapper(EntityOrm $item): MapperInterface
    {
        // decide which mapper is needed based on the incoming data
        // this needs to be able to recognise data, and sub data achieved through joins
        if ($item instanceof SecurityOrm) {
            return $this->createSecurity();
        }

        if ($item instanceof CurrencyOrm) {
            return $this->createCurrency();
        }
    }

    public function getDomainModel(EntityOrm $item): Entity
    {
        $mapper = $this->getMapper($item);
        return $mapper->getDomainModel($item);
    }

    public function createSecurity(): SecurityMapper
    {
        $settingsMapper = new SecurityMapper($this);
        return $settingsMapper;
    }

    public function createCurrency(): CurrencyMapper
    {
        $currencyMapper = new CurrencyMapper($this);
        return $currencyMapper;
    }
}
