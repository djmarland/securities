<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Data\Database\Entity\Entity as EntityOrm;
use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\Product;
use SecuritiesService\Domain\ValueObject\ID;

class ProductMapper extends Mapper
{
    public function getDomainModel(array $item): Entity
    {
        $id = new ID($item['id']);
        $product = new Product(
            $id,
            $item['number'],
            $item['name']
        );
        return $product;
    }
}
