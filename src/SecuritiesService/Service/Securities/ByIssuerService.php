<?php

namespace SecuritiesService\Service\Securities;

use Doctrine\ORM\QueryBuilder;
use SecuritiesService\Domain\Entity\Company;
use SecuritiesService\Domain\ValueObject\Bucket;
use SecuritiesService\Service\Filter\SecuritiesFilter;
use SecuritiesService\Service\SecuritiesService;

class ByIssuerService extends SecuritiesService
{
    public function find(
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        SecuritiesFilter $filter = null
    ): array {
        $qb = $this->selectWithJoins();
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

    public function findLatest(
        int $limit = self::DEFAULT_LIMIT
    ): array {
        $qb = $this->selectWithJoins();
        $qb = $this->where($qb, $this->getDomainEntity());
        $qb->orderBy(self::TBL . '.maturityDate', 'ASC');
        $qb->setMaxResults($limit);

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function findNextMaturing(
        int $limit = self::DEFAULT_LIMIT
    ): array {
        $qb = $this->selectWithJoins();
        $qb = $this->where($qb, $this->getDomainEntity());
        $qb = $this->orderByMaturing($qb);
        $qb->setMaxResults($limit);

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function sumByMonthForYear(
        int $year,
        Company $issuer = null
    ): array {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb = $this->where($qb, $issuer);
        return $this->buildSumByMonthForYear($qb, $year);
    }

    public function sumByProductForBucket(
        Bucket $bucket
    ) {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb = $this->whereAll($qb, $this->getDomainEntity());
        return $this->buildSumByProductForBucket($qb, $bucket);
    }

    private function where(
        QueryBuilder $qb,
        Company $issuer
    ) {
        $qb = $this->whereAll($qb, $issuer);
        return $this->filterLists($qb);
    }

    private function whereAll(
        QueryBuilder $qb,
        Company $issuer
    ) {
        return $qb->andWhere('IDENTITY(' . self::TBL . '.company) = :company_id')
            ->setParameter('company_id', (string) $issuer->getId()->getBinary());
    }

    private function queryForScalar(
        Company $issuer,
        SecuritiesFilter $filter = null
    ): QueryBuilder {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        if ($filter) {
            $qb = $filter->apply($qb, self::TBL);
        }
        return $this->where($qb, $issuer);
    }
}
