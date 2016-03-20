<?php

namespace SecuritiesService\Service\Securities;

use Doctrine\ORM\QueryBuilder;
use SecuritiesService\Domain\Entity\Sector;
use SecuritiesService\Service\Filter\SecuritiesFilter;
use SecuritiesService\Service\SecuritiesService;

class BySectorService extends SecuritiesService
{
    public function find(
        Sector $sector,
        SecuritiesFilter $filter = null,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $qb = $this->selectWithJoins();
        // can't use join tree as 'co' was required by default
        $qb->leftJoin('co.parentGroup', 'p');
        $qb->leftJoin('p.sector', 's');
        if ($filter) {
            $qb = $filter->apply($qb, self::TBL);
        }
        $qb = $this->where($qb, $sector);

        $qb = $this->order($qb);
        $qb->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));
        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function count(
        Sector $sector,
        SecuritiesFilter $filter = null
    ): int {
        $qb = $this->queryForScalar($sector, $filter);
        $qb->select('count(' . self::TBL . '.id)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function sum(
        Sector $sector,
        SecuritiesFilter $filter = null
    ): float {
        $qb = $this->queryForScalar($sector, $filter);
        $qb->select('sum(' . self::TBL . '.moneyRaised)');
        return (float) $qb->getQuery()->getSingleScalarResult();
    }
    
    public function findNextMaturing(
        Sector $sector,
        int $limit = self::DEFAULT_LIMIT
    ): array {
        $qb = $this->selectWithJoins();
        $qb->leftJoin('co.parentGroup', 'p');
        $qb->leftJoin('p.sector', 's');
        $qb = $this->where($qb, $sector);
        $qb = $this->orderByMaturing($qb);
        $qb->setMaxResults($limit);

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function sumByMonthForYear(
        int $year,
        Sector $sector = null
    ): array {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb = $this->where($qb, $sector);
        $qb = $this->joinTree($qb);
        return $this->buildSumByMonthForYear($qb, $year);
    }

    private function joinTree(QueryBuilder $qb): QueryBuilder
    {
        $qb->leftJoin(self::TBL . '.company', 'co');
        $qb->leftJoin('co.parentGroup', 'p');
        $qb->leftJoin('p.sector', 's');
        return $qb;
    }

    private function where(
        QueryBuilder $qb,
        Sector $sector
    ): QueryBuilder {
        return $qb->andWhere('s.id = :sId')
            ->andWhere('(' . self::TBL . '.maturityDate > :now OR ' . self::TBL . '.maturityDate IS NULL)')
            ->setParameter('now', new \DateTime()) // @todo - inject application time
            ->setParameter('sId', (string) $sector->getId()->getBinary());
    }

    private function queryForScalar(
        Sector $sector,
        SecuritiesFilter $filter = null
    ): QueryBuilder {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb = $this->joinTree($qb);
        if ($filter) {
            $qb = $filter->apply($qb, self::TBL);
        }
        return $this->where($qb, $sector);
    }
}
