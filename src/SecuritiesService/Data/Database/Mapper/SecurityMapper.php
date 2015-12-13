<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Data\Database\Entity\Entity as EntityOrm;
use SecuritiesService\Domain\Entity\Security;
use SecuritiesService\Domain\ValueObject\ISIN;
use SecuritiesService\Domain\ValueObject\ID;

class SecurityMapper extends Mapper
{
    public function getDomainModel(EntityOrm $item): Entity
    {
        $id = new ID($item->getId());
        $isin = new ISIN($item->getIsin());
        $fsa04748 = $this->mapperFactory->getDomainModel($item->getFsa04748());
        $company = $this->mapperFactory->getDomainModel($item->getCompany());
        $currency = $this->mapperFactory->getDomainModel($item->getCurrency());

        $security = new Security(
            $id,
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
            $isin,
            $item->getName(),
            $item->getStartDate(),
            $item->getMoneyRaised(),
            $fsa04748,
            $company,
            $currency,
            $item->getMaturityDate(),
            $item->getCoupon()
        );
        return $security;
    }
}
