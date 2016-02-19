<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\Product;
use SecuritiesService\Domain\ValueObject\UUID;

class ProductMapper extends Mapper
{
    public function getDomainModel(array $item): Entity
    {
        $id = new UUID($item['id']);
        $product = new Product(
            $id,
            $item['number'],
            $item['name']
        );
        return $product;
    }
}
