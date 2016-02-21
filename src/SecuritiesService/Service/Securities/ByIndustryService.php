<?php

namespace SecuritiesService\Service\Securities;

use Doctrine\ORM\QueryBuilder;
use SecuritiesService\Domain\Entity\Industry;
use SecuritiesService\Service\Filter\SecuritiesFilter;
use SecuritiesService\Service\SecuritiesService;

class ByIndustryService extends SecuritiesService
{
    public function find(
        Industry $industry,
        SecuritiesFilter $filter = null,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $qb = $this->selectWithJoins();

        $qb->leftJoin('co.parentGroup', 'p');
        $qb->leftJoin('p.sector', 's');
        $qb->leftJoin('s.industry', 'i');
        if ($filter) {
            $qb = $filter->apply($qb, self::TBL);
        }
        $qb = $this->where($qb, $industry);

        $qb->orderBy(self::TBL . '.isin', 'ASC');
        $qb->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));
        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function count(
        Industry $industry,
        SecuritiesFilter $filter = null
    ): int {
        $qb = $this->queryForScalar($industry, $filter);
        $qb->select('count(' . self::TBL . '.id)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function sum(
        Industry $industry,
        SecuritiesFilter $filter = null
    ): int {
        $qb = $this->queryForScalar($industry, $filter);
        $qb->select('sum(' . self::TBL . '.moneyRaised)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    private function where(
        QueryBuilder $qb,
        Industry $industry
    ) {
        return $qb->andWhere('s.id = :sId')
            ->setParameter('sId', (string) $industry->getId()->getBinary());
    }

    private function queryForScalar(
        Industry $industry,
        SecuritiesFilter $filter = null
    ): QueryBuilder {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->leftJoin(self::TBL . '.company', 'co');
        $qb->leftJoin('co.parentGroup', 'p');
        $qb->leftJoin('p.sector', 's');
        $qb->leftJoin('s.industry', 'i');
        if ($filter) {
            $qb = $filter->apply($qb, self::TBL);
        }
        return $this->where($qb, $industry);
    }
}
