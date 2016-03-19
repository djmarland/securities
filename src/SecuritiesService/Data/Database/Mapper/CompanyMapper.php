<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\Company;
use SecuritiesService\Domain\ValueObject\UUID;

class CompanyMapper extends Mapper
{
    public function getDomainModel(array $item): Entity
    {
        $id = new UUID($item['id']);
        $parentGroup = null;
        $country = null;

        // uses array_key_exists so we can associate a NullObject with it if needed
        if (array_key_exists('parentGroup', $item)) {
            $parentGroup = $this->mapperFactory->createParentGroup()->getDomainModel($item['parentGroup']);
        }

        if (array_key_exists('country', $item)) {
            $country = $this->mapperFactory->createCountry()->getDomainModel($item['country']);
        }

        $currency = new Company(
            $id,
            $item['name'],
            $country,
            $parentGroup
        );
        return $currency;
    }
}
