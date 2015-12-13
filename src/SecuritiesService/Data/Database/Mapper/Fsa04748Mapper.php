<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Data\Database\Entity\Entity as EntityOrm;
use SecuritiesService\Domain\Entity\Fsa04748;
use SecuritiesService\Domain\ValueObject\ID;

class Fsa04748Mapper extends Mapper
{
    public function getDomainModel(EntityOrm $item): Entity
    {
        $id = new ID($item->getId());
        $fsa04748 = new Fsa04748(
            $id,
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
            $item->getLine(),
            $item->getName()
        );
        return $fsa04748;
    }
}
