<?php

namespace SecuritiesService\Service\Securities;

use Doctrine\ORM\QueryBuilder;
use SecuritiesService\Domain\Entity\ParentGroup;
use SecuritiesService\Service\Filter\SecuritiesFilter;
use SecuritiesService\Service\SecuritiesService;

class ByGroupService extends SecuritiesService
{
    public function find(
        ParentGroup $group,
        SecuritiesFilter $filter = null,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $qb = $this->selectWithJoins();

        $qb->leftJoin('co.parentGroup', 'p');
        if ($filter) {
            $qb = $filter->apply($qb, self::TBL);
        }
        $qb = $this->where($qb, $group);

        $qb = $this->order($qb);
        $qb->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));
        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function count(
        ParentGroup $group,
        SecuritiesFilter $filter = null
    ): int {
        $qb = $this->queryForScalar($group, $filter);
        $qb->select('count(' . self::TBL . '.id)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function sum(
        ParentGroup $group,
        SecuritiesFilter $filter = null
    ): float {
        $qb = $this->queryForScalar($group, $filter);
        $qb->select('sum(' . self::TBL . '.moneyRaised)');
        return (float) $qb->getQuery()->getSingleScalarResult();
    }

    public function findNextMaturing(
        ParentGroup $group,
        int $limit = self::DEFAULT_LIMIT
    ): array {
        $qb = $this->selectWithJoins();
        $qb->leftJoin('co.parentGroup', 'p');
        $qb = $this->where($qb, $group);
        $qb = $this->orderByMaturing($qb);
        $qb->setMaxResults($limit);

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function sumByMonthForYear(
        int $year,
        ParentGroup $group = null
    ): array {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb = $this->where($qb, $group);
        $qb = $this->joinTree($qb);
        return $this->buildSumByMonthForYear($qb, $year);
    }

    private function joinTree(QueryBuilder $qb): QueryBuilder
    {
        $qb->leftJoin(self::TBL . '.company', 'co');
        $qb->leftJoin('co.parentGroup', 'p');
        return $qb;
    }

    private function where(
        QueryBuilder $qb,
        ParentGroup $group
    ) {
        return $qb->andWhere('p.id = :groupId')
            ->andWhere('(' . self::TBL . '.maturityDate > :now OR ' . self::TBL . '.maturityDate IS NULL)')
            ->setParameter('now', new \DateTime()) // @todo - inject application time
            ->setParameter('groupId', (string) $group->getId()->getBinary());
    }

    private function queryForScalar(
        ParentGroup $group,
        SecuritiesFilter $filter = null
    ): QueryBuilder {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->leftJoin(self::TBL . '.company', 'co');
        $qb->leftJoin('co.parentGroup', 'p');
        if ($filter) {
            $qb = $filter->apply($qb, self::TBL);
        }
        return $this->where($qb, $group);
    }
}
