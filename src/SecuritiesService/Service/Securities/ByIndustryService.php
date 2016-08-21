<?php

namespace SecuritiesService\Service\Securities;

use Doctrine\ORM\QueryBuilder;
use SecuritiesService\Domain\Entity\Industry;
use SecuritiesService\Domain\ValueObject\Bucket;
use SecuritiesService\Service\Filter\SecuritiesFilter;
use SecuritiesService\Service\SecuritiesService;

class ByIndustryService extends SecuritiesService
{
    public function find(
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        SecuritiesFilter $filter = null
    ): array {
        $qb = $this->selectWithJoins();
        $qb = $this->where($qb, $this->getDomainEntity());

        $qb->leftJoin('co.parentGroup', 'p');
        $qb->leftJoin('p.sector', 's');
        $qb->leftJoin('s.industry', 'i');

        return $this->buildFind($qb, $limit, $page, $filter);
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
        $qb->leftJoin('p.sector', 's');
        $qb->leftJoin('s.industry', 'i');
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
        $qb->leftJoin('p.sector', 's');
        $qb->leftJoin('s.industry', 'i');
        return $qb;
    }

    private function where(
        QueryBuilder $qb,
        Industry $industry
    ) {
        $qb = $this->whereAll($qb, $industry);
        return $this->filterLists($qb);
    }

    private function whereAll(
        QueryBuilder $qb,
        Industry $industry
    ) {
        return $qb->andWhere('i.id = :iId')
            ->setParameter('iId', (string) $industry->getId()->getBinary());
    }

    private function queryForScalar(
        Industry $industry,
        SecuritiesFilter $filter = null
    ): QueryBuilder {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb = $this->joinTree($qb);
        if ($filter) {
            $qb = $filter->apply($qb, self::TBL);
        }
        return $this->where($qb, $industry);
    }
}
