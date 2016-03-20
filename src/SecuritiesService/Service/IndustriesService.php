<?php

namespace SecuritiesService\Service;

use SecuritiesService\Domain\Entity\Industry;
use SecuritiesService\Domain\ValueObject\UUID;

class IndustriesService extends Service
{
    const SERVICE_ENTITY = 'Industry';

    public function findByUUID(
        UUID $id
    ): Industry {
        return parent::simpleFindByUUID($id, self::SERVICE_ENTITY);
    }

    public function findAll(): array
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('tbl');
        $qb->orderBy('tbl.name', 'ASC');

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function countAll(): int
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('count(' . self::TBL . '.id)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
