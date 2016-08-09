<?php

namespace SecuritiesService\Service;

use SecuritiesService\Domain\Entity\Currency;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\ValueObject\UUID;

class CurrenciesService extends Service
{
    const SERVICE_ENTITY = 'Currency';

    public function findByUUID(
        UUID $id
    ): Currency {
        return parent::simpleFindByUUID($id, self::SERVICE_ENTITY);
    }

    public function findByCode(
        string $code
    ) {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL)
            ->where(self::TBL . '.code = :code')
            ->setParameter('code', $code);

        $results = $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
        if (empty($results)) {
            throw new EntityNotFoundException('No such item with Code ' . $code);
        }

        return reset($results);
    }

    public function findAll(): array
    {
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
