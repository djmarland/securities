<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Data\Database\Entity\Entity as EntityOrm;
use SecuritiesService\Domain\Entity\Company;
use SecuritiesService\Domain\ValueObject\ID;

class CompanyMapper extends Mapper
{
    public function getDomainModel(EntityOrm $item): Entity
    {
        $id = new ID($item->getId());
        $parentGroup = $this->mapperFactory->getDomainModel($item->getParentGroup());
        $currency = new Company(
            $id,
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
            $item->getName(),
            $parentGroup
        );
        return $currency;
    }
}
