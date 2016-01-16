<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Data\Database\Entity\Entity as EntityOrm;
use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\Product;
use SecuritiesService\Domain\ValueObject\ID;

class ProductMapper extends Mapper
{
    public function getDomainModel(EntityOrm $item): Entity
    {
        $id = new ID($item->getId());
        $product = new Product(
            $id,
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
            $item->getNumber(),
            $item->getName()
        );
        return $product;
    }
}
