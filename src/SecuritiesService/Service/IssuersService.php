<?php

namespace SecuritiesService\Service;

use SecuritiesService\Domain\ValueObject\ID;

class IssuersService extends Service
{
    const COMPANY_ENTITY = 'Company';

    public function findAndCountAll(
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {

        // count them first (cheaper if zero)
        $count = $this->countAll();
        if (0 == $count) {
            return new ServiceResultEmpty();
        }

        // find the latest
        $result = $this->findAll($limit, $page);
        $result->setTotal($count);
        return $result;
    }

    public function countAll(): int
    {
        $qb = $this->getQueryBuilder(self::COMPANY_ENTITY);
        $qb->select('count(tbl.id)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findAll(
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {
        $qb = $this->getQueryBuilder(self::COMPANY_ENTITY);
        $qb->select('tbl');
        $qb->orderBy('tbl.name', 'ASC');
        $qb->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));
        $result = $qb->getQuery()->getResult();
        return $this->getServiceResult($result);
    }

    public function findByID(
        ID $id
    ): ServiceResultInterface {
        $result = $this->entityManager
            ->find('SecuritiesService:Company', $id);
        return $this->getServiceResult($result);
    }
}
