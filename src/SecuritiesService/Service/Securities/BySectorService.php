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

        $qb->orderBy(self::TBL . '.isin', 'ASC');
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
        $qb->orderBy(self::TBL . '.maturityDate', 'ASC');
        $qb->setMaxResults($limit);

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function issuanceYears(
        Sector $sector
    ): array {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb = $this->joinTree($qb);
        $qb = $this->where($qb, $sector);
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
        Sector $sector,
        int $year
    ): array {
        $productTbl = 'product';

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb = $this->where($qb, $sector);
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
