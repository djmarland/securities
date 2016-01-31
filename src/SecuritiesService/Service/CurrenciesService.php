<?php

namespace SecuritiesService\Service;

use SecuritiesService\Domain\ValueObject\ID;

class CurrenciesService extends Service
{
    const COMPANY_ENTITY = 'Currency';

    public function findAndCountAll(): ServiceResultInterface {

        // count them first (cheaper if zero)
        $count = $this->countAll();
        if (0 == $count) {
            return new ServiceResultEmpty();
        }

        // find the latest
        $result = $this->findAll();
        $result->setTotal($count);
        return $result;
    }

    public function countAll(): int
    {
        $qb = $this->getQueryBuilder(self::COMPANY_ENTITY);
        $qb->select('count(' . self::TBL . '.id)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findAll(): ServiceResultInterface {
        $qb = $this->getQueryBuilder(self::COMPANY_ENTITY);
        $qb->select(self::TBL);
        $qb->orderBy(self::TBL . '.code', 'ASC');
        $result = $qb->getQuery()->getResult();
        return $this->getServiceResult($result);
    }

    public function findByID(
        ID $id
    ): ServiceResultInterface {

        $qb = $this->getQueryBuilder(self::COMPANY_ENTITY);
        $qb->select(self::TBL)
            ->where(self::TBL . '.id = :id')
            ->setParameters([
                'id' => $id
            ]);

        $result = $qb->getQuery()->getResult();
        return $this->getServiceResult($result);
    }
}
