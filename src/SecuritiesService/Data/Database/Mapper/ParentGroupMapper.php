<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Data\Database\Entity\Entity as EntityOrm;
use SecuritiesService\Domain\Entity\ParentGroup;
use SecuritiesService\Domain\ValueObject\ID;

class ParentGroupMapper extends Mapper
{
    public function getDomainModel(EntityOrm $item): Entity
    {
        $id = new ID($item->getId());
        $currency = new ParentGroup(
            $id,
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
            $item->getName()
        );
        return $currency;
    }
}
