<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\Company;
use SecuritiesService\Domain\ValueObject\ID;

class CompanyMapper extends Mapper
{
    public function getDomainModel(array $item): Entity
    {
        $id = new ID($item['id']);
        $parentGroup = null;
        $country = null;

        if (isset($item['parentGroup'])) {
            $parentGroup = $this->mapperFactory->createParentGroup()->getDomainModel($item['parentGroup']);
        }

        if (isset($item['country'])) {
            $parentGroup = $this->mapperFactory->createCountry()->getDomainModel($item['country']);
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
