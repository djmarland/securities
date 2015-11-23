<?php

namespace DatabaseBundle\Mapper;

use AppBundle\Domain\Entity\Entity;
use DatabaseBundle\Entity\Entity as EntityOrm;
use AppBundle\Domain\Entity\Fsa04748;
use AppBundle\Domain\ValueObject\ID;

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
