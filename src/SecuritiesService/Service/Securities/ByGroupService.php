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
        $qb->orderBy(self::TBL . '.maturityDate', 'ASC');
        $qb->setMaxResults($limit);

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function issuanceYears(
        ParentGroup $group
    ): array {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->leftJoin(self::TBL . '.company', 'co');
        $qb->leftJoin('co.parentGroup', 'p');
        $qb = $this->where($qb, $group);
        $qb->select([
            'DATE_FORMAT(' . self::TBL . '.startDate, \'%Y\') as y',
        ])
            ->distinct()
            ->orderBy('y', 'DESC');
        $results = $qb->getQuery()->getArrayResult();
        return array_map(function ($result) {
            return $result['y'];
        }, $results);
    }

    public function productCountsByMonthForYear(
        ParentGroup $group,
        int $year
    ): array {
        $productTbl = 'product';

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb = $this->where($qb, $group);
        $qb->select([
            self::TBL,
            'DATE_FORMAT(' . self::TBL . '.startDate, \'%m\') as m',
            'count(' . self::TBL . '.id) as c',
            $productTbl,
        ])
            ->andWhere('DATE_FORMAT(' . self::TBL . '.startDate, \'%Y\') = :year');

        $qb->leftJoin(self::TBL . '.company', 'co');
        $qb->leftJoin('co.parentGroup', 'p');
        $qb->leftJoin(self::TBL . '.product', $productTbl);
        $qb->groupBy($productTbl . '.id', 'm');

        $qb->setParameter('year', (string) $year);

        /*
         * List of:
         * 0 => Security
         * c => count
         * m => month
        */
        $results = $qb->getQuery()->getArrayResult();
        $months = [];
        $mapper = $this->mapperFactory->createMapper('Product');
        foreach ($results as $result) {
            $product = $mapper->getDomainModel($result[0]['product']);
            $total = (int) $result['c'];
            $month = (int) $result['m'];
            if (!isset($months[$month])) {
                $months[$month] = [];
            }
            $months[$month][(string) $product->getId()] = (object) [
                'product' => $product,
                'total' => $total,
            ];
        }
        return $months;
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
