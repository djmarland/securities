<?php

namespace SecuritiesService\Service;

use Doctrine\ORM\QueryBuilder;
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
        return $this->getServiceResult($qb);
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

        return $this->getServiceResult($qb);
    }

    protected function getServiceResult(QueryBuilder $qb, $type = 'Currency')
    {
        return parent::getServiceResult($qb, $type);
    }
}
