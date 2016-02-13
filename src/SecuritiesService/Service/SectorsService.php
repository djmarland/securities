<?php

namespace SecuritiesService\Service;

use Doctrine\ORM\QueryBuilder;
use SecuritiesService\Domain\Entity\Industry;
use SecuritiesService\Domain\ValueObject\ID;

class SectorsService extends Service
{
    const SERVICE_ENTITY = 'Sector';

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
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('count(' . self::TBL . '.id)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findAll(
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL);
        $qb->orderBy(self::TBL . '.name', 'ASC');
        $qb->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));
        return $this->getServiceResult($qb);
    }

    public function findAndCountAllByIndustry(
        Industry $industry,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {

        // count them first (cheaper if zero)
        $count = $this->countAllByIndustry($industry);
        if (0 == $count) {
            return new ServiceResultEmpty();
        }

        // find the latest
        $result = $this->findAllByIndustry($industry, $limit, $page);
        $result->setTotal($count);
        return $result;
    }

    public function countAllByIndustry(
        Industry $indsutry
    ): int {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('count(' . self::TBL . '.id)')
            ->where('IDENTITY(' . self::TBL . '.industry) = :industry_id')
            ->setParameters([
                'industry_id' => (string) $indsutry->getId()
            ]);
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findAllByIndustry(
        Industry $industry,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL)
            ->where('IDENTITY(' . self::TBL . '.industry) = :industry_id')
            ->orderBy(self::TBL . '.name', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page))
            ->setParameters([
                'industry_id' => (string) $industry->getId()
            ]);

        return $this->getServiceResult($qb);
    }

    public function findByID(
        ID $id
    ): ServiceResultInterface {
        $industryTbl = 'i';

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL, $industryTbl)
            ->where(self::TBL . '.id = :id')
            ->leftJoin(self::TBL . '.industry', $industryTbl)
            ->setParameters([
                'id' => $id
            ]);

        return $this->getServiceResult($qb);
    }

    public function findAllInIndustries(): ServiceResultInterface {
        $industryTbl = 'i';

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL, $industryTbl)
            ->leftJoin(self::TBL . '.industry', $industryTbl)
            ->addOrderBy($industryTbl . '.name', 'ASC')
            ->addOrderBy(self::TBL . '.name', 'ASC');

        return $this->getServiceResult($qb);
    }

    protected function getServiceResult(QueryBuilder $qb, $type = 'Sector')
    {
        return parent::getServiceResult($qb, $type);
    }
}
