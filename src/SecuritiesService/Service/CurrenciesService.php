<?php

namespace SecuritiesService\Service;

use SecuritiesService\Domain\Entity\Currency;
use SecuritiesService\Domain\ValueObject\UUID;

class CurrenciesService extends Service
{
    const SERVICE_ENTITY = 'Currency';

    public function findByUUID(
        UUID $id
    ): Currency {
        parent::simpleFindByUUID($id, self::SERVICE_ENTITY);
    }

    public function findByCode(
        $code
    ) {
        // @todo - currencies should use codes
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
