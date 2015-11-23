<?php

namespace DatabaseBundle\Mapper;

use AppBundle\Domain\Entity\Entity;
use DatabaseBundle\Entity\Entity as EntityOrm;
use AppBundle\Domain\Entity\Company;
use AppBundle\Domain\ValueObject\ID;

class CompanyMapper extends Mapper
{
    public function getDomainModel(EntityOrm $item): Entity
    {
        $id = new ID($item->getId());
        $currency = new Company(
            $id,
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
            $item->getName()
        );
        return $currency;
    }
}
