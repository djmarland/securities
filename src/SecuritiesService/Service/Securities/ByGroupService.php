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

        $qb->orderBy(self::TBL . '.isin', 'ASC');
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
    ): int {
        $qb = $this->queryForScalar($group, $filter);
        $qb->select('sum(' . self::TBL . '.money_raised)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    private function where(
        QueryBuilder $qb,
        ParentGroup $group
    ) {
        return $qb->andWhere('p.id = :group_id')
            ->setParameter('group_id', (string) $group->getId());
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