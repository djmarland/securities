<?php

namespace SecuritiesService\Service;

use Doctrine\ORM\QueryBuilder;
use SecuritiesService\Domain\Entity\Currency;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\ValueObject\ID;

class CurrenciesService extends Service
{
    const SERVICE_ENTITY = 'Currency';

    public function findByID(
        ID $id
    ): Currency {
        parent::simplefindById($id, self::SERVICE_ENTITY);
    }

    public function findAll(): array {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL);
        $qb->orderBy(self::TBL . '.code', 'ASC');
        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function countAll(): int
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('count(' . self::TBL . '.id)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
