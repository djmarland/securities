<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Data\Database\Entity\Entity as EntityOrm;
use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\Line;
use SecuritiesService\Domain\ValueObject\ID;

class LineMapper extends Mapper
{
    public function getDomainModel(EntityOrm $item): Entity
    {
        $id = new ID($item->getId());
        $line = new Line(
            $id,
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
            $item->getNumber(),
            $item->getName()
        );
        return $line;
    }
}
