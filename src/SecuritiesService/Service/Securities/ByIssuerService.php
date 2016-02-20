<?php

namespace SecuritiesService\Service\Securities;

use Doctrine\ORM\QueryBuilder;
use SecuritiesService\Domain\Entity\Company;
use SecuritiesService\Service\Filter\SecuritiesFilter;
use SecuritiesService\Service\SecuritiesService;

class ByIssuerService extends SecuritiesService
{
    public function find(
        Company $issuer,
        SecuritiesFilter $filter = null,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $qb = $this->selectWithJoins();
        if ($filter) {
            $qb = $filter->apply($qb, self::TBL);
        }
        $qb = $this->where($qb, $issuer);
        $qb->orderBy(self::TBL . '.isin', 'ASC');
        $qb->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function count(
        Company $issuer,
        SecuritiesFilter $filter = null
    ): int {
        $qb = $this->queryForScalar($issuer, $filter);
        $qb->select('count(' . self::TBL . '.id)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function sum(
        Company $issuer,
        SecuritiesFilter $filter = null
    ): int {
        $qb = $this->queryForScalar($issuer, $filter);
        $qb->select('sum(' . self::TBL . '.money_raised)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findLatest(
        Company $issuer,
        int $limit = self::DEFAULT_LIMIT
    ): array {
        $qb = $this->selectWithJoins();
        $qb = $this->where($qb, $issuer);
        $qb->orderBy(self::TBL . '.isin', 'ASC');
        $qb->setMaxResults($limit);

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);;
    }


    private function where(
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


    /* Special Counts */
    public function productCountsByMonthForYear(
        Company $issuer,
        int $year
    ): array {
        /*
         * select DATE_FORMAT(start_date, '%m') as m, p.name, count(*)
         * from securities left join products as p on product_id = p.id
         * where company_id = 29 and DATE_FORMAT(start_date, '%Y') = "2012" group by p.name,m;
         */
        $productTbl = 'product';

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb = $this->where($qb, $issuer);
        $qb->select([
            self::TBL,
            'DATE_FORMAT(' . self::TBL . '.start_date, \'%m\') as m',
            'count(' . self::TBL . '.id) as c',
            $productTbl
        ])
            ->andWhere('DATE_FORMAT(' . self::TBL . '.start_date, \'%Y\') = :year');

        $qb->leftJoin(self::TBL . '.product', $productTbl);
        $qb->groupBy($productTbl . '.id','m');

        $qb->setParameters([
            'company_id' => (string) $issuer->getId()->getBinary(),
            'year' => (string) $year
        ]);

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
            $months[$month][(int) $product->getId()->getValue()] = (object) [
                'product' => $product,
                'total' => $total
            ];
        }
        return $months;
    }

}
