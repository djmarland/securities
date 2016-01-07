<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Data\Database\Entity\Entity as EntityOrm;
use SecuritiesService\Data\Database\Entity\Line as LineOrm;
use SecuritiesService\Data\Database\Entity\Security as SecurityOrm;
use SecuritiesService\Data\Database\Entity\Company as CompanyOrm;
use SecuritiesService\Data\Database\Entity\Currency as CurrencyOrm;

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

        if ($item instanceof CompanyOrm) {
            return $this->createCompany();
        }

        if ($item instanceof CurrencyOrm) {
            return $this->createCurrency();
        }

        if ($item instanceof LineOrm) {
            return $this->createLine();
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

    public function createLine(): LineMapper
    {
        $lineMapper = new LineMapper($this);
        return $lineMapper;
    }

    public function createCompany(): CompanyMapper
    {
        $companyMapper = new CompanyMapper($this);
        return $companyMapper;
    }

    public function createCurrency(): CurrencyMapper
    {
        $currencyMapper = new CurrencyMapper($this);
        return $currencyMapper;
    }
}
