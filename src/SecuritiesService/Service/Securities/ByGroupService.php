<?php

namespace SecuritiesService\Service\Securities;

use Doctrine\ORM\QueryBuilder;
use SecuritiesService\Domain\Entity\ParentGroup;
use SecuritiesService\Domain\ValueObject\Bucket;
use SecuritiesService\Service\Filter\SecuritiesFilter;
use SecuritiesService\Service\SecuritiesService;

class ByGroupService extends SecuritiesService
{
    public function find(
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        SecuritiesFilter $filter = null
    ): array {
        $qb = $this->selectWithJoins();

        $qb->leftJoin('co.parentGroup', 'p');
        if ($filter) {
            $qb = $filter->apply($qb, self::TBL);
        }
        $qb = $this->where($qb, $this->getDomainEntity());

        $qb = $this->order($qb);
        $qb->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));
        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function count(
        SecuritiesFilter $filter = null
    ): int {
        $qb = $this->queryForScalar($this->getDomainEntity(), $filter);
        $qb->select('count(' . self::TBL . '.id)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function sum(
        SecuritiesFilter $filter = null
    ): float {
        $qb = $this->queryForScalar($this->getDomainEntity(), $filter);
        $qb->select('sum(' . self::TBL . '.moneyRaised)');
        return (float) $qb->getQuery()->getSingleScalarResult();
    }

    public function findNextMaturing(
        int $limit = self::DEFAULT_LIMIT
    ): array {
        $qb = $this->selectWithJoins();
        $qb->leftJoin('co.parentGroup', 'p');
        $qb = $this->where($qb, $this->getDomainEntity());
        return $this->buildNextMaturing($qb, $limit);
    }

    public function sumByMonthForYear(
        int $year
    ): array {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb = $this->where($qb, $this->getDomainEntity());
        $qb = $this->joinTree($qb);
        return $this->buildSumByMonthForYear($qb, $year);
    }

    public function sumByProductForBucket(
        Bucket $bucket
    ) {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb = $this->whereAll($qb, $this->getDomainEntity());
        $qb = $this->joinTree($qb);
        return $this->buildSumByProductForBucket($qb, $bucket);
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
        $qb = $this->whereAll($qb, $group);
        return $this->filterExpired($qb);
    }

    private function whereAll(
        QueryBuilder $qb,
        ParentGroup $group
    ) {
        return $qb->andWhere('p.id = :groupId')
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
