<?php

namespace SecuritiesService\Service;

use DateTimeImmutable;
use SecuritiesService\Domain\Entity\Company;
use SecuritiesService\Domain\Entity\Currency;
use SecuritiesService\Domain\Entity\Product;
use SecuritiesService\Domain\Entity\Security;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\ValueObject\Bucket;
use SecuritiesService\Domain\ValueObject\BucketUndated;
use SecuritiesService\Domain\ValueObject\ISIN;
use SecuritiesService\Service\Filter\SecuritiesFilter;

class SecuritiesService extends Service
{
    const SERVICE_ENTITY = 'Security';

    /* Individual */
    public function findByIsin(ISIN $isin): Security
    {
        $qb = $this->selectWithJoins()
            ->where(self::TBL . '.isin = :isin')
            ->setParameter('isin', (string) $isin);

        $results = $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
        if (empty($results)) {
            throw new EntityNotFoundException();
        }

        return reset($results);
    }


    /* All */
    public function findAll(
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $qb = $this->selectWithJoins();
        $qb->orderBy(self::TBL . '.isin', 'ASC');
        $qb = $this->paginate($qb, $limit, $page);
        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function countAll(): int
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('count(' . self::TBL . '.id)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function sumAll(): int
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('sum(' . self::TBL . '.money_raised)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /* Filtered */
    public function findAllFiltered(
        SecuritiesFilter $filter,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $qb = $this->selectWithJoins();
        $qb = $filter->apply($qb, self::TBL);
        $qb->orderBy(self::TBL . '.isin', 'ASC');
        $qb = $this->paginate($qb, $limit, $page);
        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }


    public function countAllFiltered(
        SecuritiesFilter $filter
    ): int {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('count(' . self::TBL . '.id)');
        $qb = $filter->apply($qb, self::TBL);
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function sumAllFiltered(
        SecuritiesFilter $filter
    ): int {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('sum(' . self::TBL . '.money_raised)');
        $qb = $filter->apply($qb, self::TBL);
        return (int) $qb->getQuery()->getSingleScalarResult();
    }


//
//    public function sumForProductGroupedByCurrencyForYearToDate(
//        DateTimeImmutable $endDate,
//        Product $product = null
//    ) {
//        /*
//         * select DATE_FORMAT(start_date, '%m') as m, p.name, count(*)
//         * from securities left join products as p on product_id = p.id
//         * where company_id = 29 and DATE_FORMAT(start_date, '%Y') = "2012" group by p.name,m;
//         */
//        $currencyTbl = 'product';
//
//        $year = $endDate->format('Y');
//
//        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
//        $qb->select([
//            self::TBL,
//            'sum(' . self::TBL . '.money_raised) as s',
//            $currencyTbl
//        ])
//            ->where('DATE_FORMAT(' . self::TBL . '.start_date, \'%Y\') = :year')
//            ->andWhere(self::TBL . '.start_date <= :end_date')
//            ->leftJoin(self::TBL . '.currency', $currencyTbl);
//
//        $params =[
//            'year' => $year,
//            'end_date' => $endDate
//        ];
//
//        if ($product) {
//            $qb->andWhere('IDENTITY(' . self::TBL . '.product) = :product_id');
//            $params['product_id'] = (string) $product->getId();
//        }
//
//        $qb->groupBy($currencyTbl . '.id')
//            ->setParameters($params);
//
//
//        /*
//         * List of:
//         * 0 => Security
//         * c => count
//         * m => month
//        */
//        $results = $qb->getQuery()->getArrayResult();
//
//        $currencies = [];
//        foreach ($results as $result) {
//            $currency = $this->getDomainModel($result[0]['currency'], 'Currency');
//            $code = $currency->getCode();
//            $total = (int) $result['s'];
//            $currencies[$code] = $total;
//        }
//
//        return $currencies;
//    }

//    public function sumForProductGroupedByCountryForYearToDate(
//        DateTimeImmutable $endDate,
//        Product $product = null
//    ) {
//        /*
//         * select c.name, sum(*)
//         * from securities left join products as p on product_id = p.id
//         * where company_id = 29 and DATE_FORMAT(start_date, '%Y') = "2012" group by p.name,m;
//         */
//        $companyTbl = 'company';
//        $countryTbl = 'country';
//
//        $year = $endDate->format('Y');
//
//        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
//        $qb->select([
//            self::TBL,
//            'sum(' . self::TBL . '.money_raised) as s',
//            $companyTbl,
//            $countryTbl
//        ])
//            ->where('DATE_FORMAT(' . self::TBL . '.start_date, \'%Y\') = :year')
//            ->andWhere(self::TBL . '.start_date <= :end_date')
//            ->leftJoin(self::TBL . '.company', $companyTbl)
//            ->leftJoin($companyTbl . '.country', $countryTbl);
//
//        $params =[
//            'year' => $year,
//            'end_date' => $endDate
//        ];
//
//        if ($product) {
//            $qb->andWhere('IDENTITY(' . self::TBL . '.product) = :product_id');
//            $params['product_id'] = (string) $product->getId();
//        }
//
//        $qb->groupBy($countryTbl . '.id')
//            ->setParameters($params);
//
//
//        /*
//         * List of:
//         * 0 => Security
//         * s => sum
//        */
//        $results = $qb->getQuery()->getArrayResult();
//
//        $countries = [];
//        foreach ($results as $result) {
//            $country = $this->getDomainModel($result[0]['company']['country'], 'Country');
//            $code = $country->getName();
//            $total = (int) $result['s'];
//            $countries[$code] = $total;
//        }
//
//        return $countries;
//    }

    public function countsByProduct()
    {
        /*
         * select p.name, count(s.id)
         * from securities as s left join products as p on s.product_id = p.id
         * group by p.id;
         */
        $productTable = 'product';

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select([
            self::TBL,
            'count(' . self::TBL . '.id) as c',
            $productTable,
        ])
            ->leftJoin(self::TBL . '.product', $productTable);

        $qb->groupBy($productTable . '.id');

        /*
         * List of:
         * 0 => Security
         * c => count
        */
        $results = $qb->getQuery()->getArrayResult();

        $products = [];
        $mapper = $this->mapperFactory->createMapper('Product');
        foreach ($results as $result) {
            $product = $mapper->getDomainModel($result[0]['product']);
            $total = (int) $result['c'];
            $products[] = (object) [
                'product' => $product,
                'count' => $total,
            ];
        }

        return $products;
    }

    public function findUpcomingMaturities(
        DateTimeImmutable $dateFrom,
        int $limit = self::DEFAULT_LIMIT
    ): array {
        $qb = $this->selectWithJoins();
        $qb->where(self::TBL . '.maturityDate > :end_from')
            ->orderBy(self::TBL . '.maturityDate', 'ASC')
            ->setMaxResults($limit)
            ->setParameter('end_from', $dateFrom);
        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }



    protected function selectWithJoins()
    {
        $currency = 'c';
        $company = 'co';
        $product = 'product';

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL, $currency, $company, $product);
        $qb->leftJoin(self::TBL . '.currency', $currency);
        $qb->leftJoin(self::TBL . '.company', $company);
        $qb->leftJoin(self::TBL . '.product', $product);
        return $qb;
    }
}
