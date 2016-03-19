<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\Null\NullParentGroup;
use SecuritiesService\Domain\Entity\ParentGroup;
use SecuritiesService\Domain\ValueObject\UUID;

class ParentGroupMapper extends Mapper
{
    public function getDomainModel(array $item = null): Entity
    {
        if (!$item) {
            // a group was called, but the result was null
            return new NullParentGroup();
        }

        $id = new UUID($item['id']);
        $sector = null;

        // uses array_key_exists so we can associate a NullObject with it if needed
        if (array_key_exists('sector', $item)) {
            $sector = $this->mapperFactory->createSector()->getDomainModel($item['sector']);
        }

        $group = new ParentGroup(
            $id,
            $item['name'],
            $sector
        );
        return $group;
    }
}
