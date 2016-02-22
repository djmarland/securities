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
    ): float {
        $qb = $this->queryForScalar($industry, $filter);
        $qb->select('sum(' . self::TBL . '.moneyRaised)');
        return (float) $qb->getQuery()->getSingleScalarResult();
    }

    public function findNextMaturing(
        Industry $industry,
        int $limit = self::DEFAULT_LIMIT
    ): array {
        $qb = $this->selectWithJoins();
        $qb->leftJoin('co.parentGroup', 'p');
        $qb->leftJoin('p.sector', 's');
        $qb->leftJoin('s.industry', 'i');
        $qb = $this->where($qb, $industry);
        $qb->orderBy(self::TBL . '.maturityDate', 'ASC');
        $qb->setMaxResults($limit);

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }


    public function issuanceYears(
        Industry $industry
    ): array {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb = $this->joinTree($qb);
        $qb = $this->where($qb, $industry);
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
        Industry $industry,
        int $year
    ): array {
        $productTbl = 'product';

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb = $this->where($qb, $industry);
        $qb->select([
            self::TBL,
            'DATE_FORMAT(' . self::TBL . '.startDate, \'%m\') as m',
            'count(' . self::TBL . '.id) as c',
            $productTbl,
        ])
            ->andWhere('DATE_FORMAT(' . self::TBL . '.startDate, \'%Y\') = :year');

        $qb = $this->joinTree($qb);
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
        return $qb->andWhere('i.id = :iId')
            ->andWhere(self::TBL . '.maturityDate > :now')
            ->setParameter('now', new \DateTime()) // @todo - inject application time
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
