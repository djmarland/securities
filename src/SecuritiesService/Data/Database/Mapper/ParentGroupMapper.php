<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Data\Database\Entity\Entity as EntityOrm;
use SecuritiesService\Domain\Entity\ParentGroup;
use SecuritiesService\Domain\ValueObject\ID;

class ParentGroupMapper extends Mapper
{
    public function getDomainModel(array $item): Entity
    {
        $id = new ID($item['id']);
        $currency = new ParentGroup(
            $id,
            $item['name']
        );
        return $currency;
    }
}
