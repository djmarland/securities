<?php

namespace SecuritiesService\Service;

use Doctrine\ORM\QueryBuilder;
use SecuritiesService\Domain\Entity\ParentGroup;

class CountriesService extends Service
{
    const SERVICE_ENTITY = 'Country';

    public function findAll(): array
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL);
        $qb->orderBy(self::TBL . '.name', 'ASC');
        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }
}
