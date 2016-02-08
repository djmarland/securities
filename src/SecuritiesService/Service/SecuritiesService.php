<?php

namespace SecuritiesService\Service;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\QueryBuilder;
use SecuritiesService\Domain\Entity\Company;
use SecuritiesService\Domain\Entity\Currency;
use SecuritiesService\Domain\Entity\ParentGroup;
use SecuritiesService\Domain\Entity\Product;
use SecuritiesService\Domain\ValueObject\Bucket;
use SecuritiesService\Domain\ValueObject\BucketUndated;
use SecuritiesService\Domain\ValueObject\ISIN;

class SecuritiesService extends Service
{
    const SECURITY_ENTITY = 'Security';

    private function selectWithJoins()
    {
        $currency = 'c';
        $company = 'co';
        $product = 'product';

        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select(self::TBL, $currency, $company, $product);
        $qb->leftJoin(self::TBL . '.currency', $currency);
        $qb->leftJoin(self::TBL . '.company', $company);
        $qb->leftJoin(self::TBL . '.product', $product);
        return $qb;
    }

    public function findAndCountAll(
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {

        // count them first (cheaper if zero)
        $count = $this->countAll();
        if ($count == 0) {
            return new ServiceResultEmpty();
        }

        // find the latest
        $result = $this->findAll($limit, $page);
        $result->setTotal($count);
        return $result;
    }

    public function countAll(): int
    {
        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select('count(' . self::TBL . '.id)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findAll(
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {
        $qb = $this->selectWithJoins();
        $qb->orderBy(self::TBL . '.isin', 'ASC');
        $qb->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));
        return $this->getServiceResult($qb);
    }

    public function findAndCountByGroup(
        ParentGroup $group,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        SecuritiesFilter $filter = null
    ): ServiceResultInterface {
        // count them first (cheaper if zero)
        $count = $this->countByGroup(
            $group,
            $filter
        );
        if ($count == 0) {
            return new ServiceResultEmpty();
        }

        // find the latest
        $result = $this->findByGroup(
            $group,
            $limit,
            $page,
            $filter
        );
        $result->setTotal($count);
        return $result;
    }

    public function countByGroup(
        ParentGroup $group,
        SecuritiesFilter $filter = null
    ): int {
        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select('count(' . self::TBL . '.id)');

        $qb->leftJoin(self::TBL . '.company', 'co');
        $qb->leftJoin('co.parentGroup', 'p');
        if ($filter) {
            $qb = $filter->apply($qb, self::TBL);
        }
        $qb->andWhere('p.id = :group_id')
            ->setParameter('group_id', (string) $group->getId());

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findByGroup(
        ParentGroup $group,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        SecuritiesFilter $filter = null
    ): ServiceResultInterface {
        $qb = $this->selectWithJoins();

        $qb->leftJoin('co.parentGroup', 'p');
        if ($filter) {
            $qb = $filter->apply($qb, self::TBL);
        }
        $qb->andWhere('p.id = :group_id')
            ->setParameter('group_id', (string) $group->getId());

        $qb->orderBy(self::TBL . '.isin', 'ASC');
        $qb->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));
        return $this->getServiceResult($qb);
    }

    public function sumByGroup(
        ParentGroup $group,
        SecuritiesFilter $filter = null
    ): int {
        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->leftJoin(self::TBL . '.company', 'co');
        $qb->leftJoin('co.parentGroup', 'p');
        if ($filter) {
            $qb = $filter->apply($qb, self::TBL);
        }
        $qb->andWhere('p.id = :group_id')
            ->setParameter('group_id', (string) $group->getId());

        $qb->select('sum(' . self::TBL . '.money_raised)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findAndCountAllWithFilters(
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        Product $product = null,
        Currency $currency = null,
        Company $issuer = null,
        Bucket $bucket = null
    ): ServiceResultInterface {

        // count them first (cheaper if zero)
        $count = $this->countAllWithFilters(
            $product,
            $currency,
            $issuer,
            $bucket);
        if ($count == 0) {
            return new ServiceResultEmpty();
        }

        // find the latest
        $result = $this->findAllWithFilters(
            $limit,
            $page,
            $product,
            $currency,
            $issuer,
            $bucket
        );
        $result->setTotal($count);
        return $result;
    }

    public function countAllWithFilters(
        Product $product = null,
        Currency $currency = null,
        Company $issuer = null,
        Bucket $bucket = null
    ): int {
        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select('count(' . self::TBL . '.id)');
        $qb = $this->addFilters(
            $qb,
            $product,
            $currency,
            $issuer,
            $bucket
        );
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function sumAllWithFilters(
        Product $product = null,
        Currency $currency = null,
        Company $issuer = null,
        Bucket $bucket = null
    ): int {
        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select('sum(' . self::TBL . '.money_raised)');
        $qb = $this->addFilters(
            $qb,
            $product,
            $currency,
            $issuer,
            $bucket
        );
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findAllWithFilters(
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        Product $product = null,
        Currency $currency = null,
        Company $issuer = null,
        Bucket $bucket = null
    ): ServiceResultInterface {
        $qb = $this->selectWithJoins();
        $qb = $this->addFilters(
            $qb,
            $product,
            $currency,
            $issuer,
            $bucket
        );
        $qb->orderBy(self::TBL . '.isin', 'ASC');
        $qb->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));
        return $this->getServiceResult($qb);
    }

    private function addFilters(
        QueryBuilder $qb,
        Product $product = null,
        Currency $currency = null,
        Company $issuer = null,
        Bucket $bucket = null
    ):QueryBuilder {
        if ($product) {
            $qb->andWhere('IDENTITY(' . self::TBL . '.product) = :product_id')
                ->setParameter('product_id', $product->getId());
        }
        if ($currency) {
            $qb->andWhere('IDENTITY(' . self::TBL . '.currency) = :currency_id')
                ->setParameter('currency_id', $currency->getId());
        }
        if ($issuer) {
            $qb->andWhere('IDENTITY(' . self::TBL . '.company) = :company_id')
                ->setParameter('company_id', $issuer->getId());
        }
        if ($bucket) {
            if ($bucket instanceof BucketUndated) {
                $qb->andWhere(self::TBL . '.maturity_date is NULL');
            } else {
                $qb->andWhere(self::TBL . '.maturity_date > :maturity_date_lower')
                    ->andWhere(self::TBL . '.maturity_date <= :maturity_date_upper')
                    ->setParameter('maturity_date_lower', $bucket->getStartDate())
                    ->setParameter('maturity_date_upper', $bucket->getEndDate());
            }
        }
        return $qb;
    }

    public function searchAndCount(
        string $query,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {
        $count = $this->countSearch($query);

        if ($count === 0) {
            return new ServiceResultEmpty();
        }

        // find them
        $result = $this->search($query, $limit, $page);
        $result->setTotal($count);
        return $result;
    }

    public function countSearch(string $query): int
    {
        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select('count(' . self::TBL . '.id)');
        $qb->where(self::TBL . '.name LIKE ?0');
        $qb->setParameters(['%' . $query . '%']);
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function search(
        string $query,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {
        $qb = $this->selectWithJoins();
        $qb->where(self::TBL . '.name LIKE :query');
        $qb->setParameters(['query' => '%' . $query . '%']);

        $qb->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));
        return $this->getServiceResult($qb);
    }

    public function findByIsin(ISIN $isin): ServiceResultInterface
    {
        $qb = $this->selectWithJoins()
            ->where(self::TBL . '.isin = :isin')
            ->setParameters([
                'isin' => $isin
            ]);

        return $this->getServiceResult($qb);
    }

    public function findAndCountByIssuer(
        Company $issuer,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {
        return $this->findAndCountByIssuerAndProduct($issuer, null, $limit, $page);
    }

    public function findAndCountByIssuerAndProduct(
        Company $issuer,
        Product $product = null,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {

        // count them first (cheaper if zero)
        $count = $this->countByIssuerAndProduct($issuer, $product);
        if ($count == 0) {
            return new ServiceResultEmpty();
        }

        // find the latest
        $result = $this->findByIssuerAndProduct($issuer, $product, $limit, $page);
        $result->setTotal($count);
        return $result;
    }

    public function countByIssuer(Company $issuer): int
    {
        return $this->countByIssuerAndProduct($issuer, null);
    }

    public function countByIssuerAndProduct(
        Company $issuer,
        Product $product = null
    ): int {
        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select('count(' . self::TBL . '.id)')
            ->where('IDENTITY(' . self::TBL . '.company) = :company_id');
        $parameters = ['company_id' => (string) $issuer->getId()];
        if ($product) {
            $qb->andWhere('IDENTITY(' . self::TBL . '.product) = :product_id');
            $parameters['product_id'] = (string) $product->getId();
        }
        $qb->setParameters($parameters);
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findLatestForIssuer(
        Company $issuer,
        int $limit = self::DEFAULT_LIMIT
    ): ServiceResultInterface {
        $qb = $this->selectWithJoins();
        $qb->where('IDENTITY(' . self::TBL . '.company) = :company_id')
            ->orderBy(self::TBL . '.start_date', 'DESC')
            ->setMaxResults($limit);
        $parameters = ['company_id' => (string) $issuer->getId()];
        $qb->setParameters($parameters);
        return $this->getServiceResult($qb);
    }

    public function findByIssuer(
        Company $issuer,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {
        return $this->findByIssuerAndProduct($issuer, null, $limit, $page);
    }

    public function findByIssuerAndProduct(
        Company $issuer,
        Product $product = null,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {
        $qb = $this->selectWithJoins();
        $qb->where('IDENTITY(' . self::TBL . '.company) = :company_id')
            ->orderBy(self::TBL . '.isin', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));
        $parameters = ['company_id' => (string) $issuer->getId()];
        if ($product) {
            $qb->andWhere('IDENTITY(' . self::TBL . '.product) = :product_id');
            $parameters['product_id'] = (string) $product->getId();
        }
        $qb->setParameters($parameters);
        return $this->getServiceResult($qb);
    }

    public function sumByIssuer(
        Company $issuer
    ): int {
        return $this->sumByIssuerAndProduct($issuer, null);
    }

    public function sumByIssuerAndProduct(
        Company $issuer,
        Product $product = null
    ): int {
        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select('sum(' . self::TBL . '.money_raised)')
            ->where('IDENTITY(' . self::TBL . '.company) = :company_id');
        $parameters = ['company_id' => (string) $issuer->getId()];
        if ($product) {
            $qb->andWhere('IDENTITY(' . self::TBL . '.product) = :product_id');
            $parameters['product_id'] = (string) $product->getId();
        }
        $qb->setParameters($parameters);
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function sumByIssuerProductAndBucket(
        Company $issuer,
        Product $product,
        Bucket $bucket
    ): int {
        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select('sum(' . self::TBL . '.money_raised)')
            ->where('IDENTITY(' . self::TBL . '.company) = :company_id')
            ->andWhere('IDENTITY(' . self::TBL . '.product) = :product_id');
        $parameters = [
            'company_id' => (string) $issuer->getId(),
            'product_id' => (string) $product->getId()
        ];
        if ($bucket instanceof BucketUndated) {
            $qb->andWhere(self::TBL . '.maturity_date is NULL');
        } else {
            $qb->andWhere(self::TBL . '.maturity_date > :maturity_date_lower')
                ->andWhere(self::TBL . '.maturity_date <= :maturity_date_upper');
            $parameters['maturity_date_lower'] = $bucket->getStartDate();
            $parameters['maturity_date_upper'] = $bucket->getEndDate();
        }

        $qb->setParameters($parameters);
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function countProductsByIssuerForYear(
        Company $issuer,
        int $year
    ) {
        /*
         * select DATE_FORMAT(start_date, '%m') as m, p.name, count(*)
         * from securities left join products as p on product_id = p.id
         * where company_id = 29 and DATE_FORMAT(start_date, '%Y') = "2012" group by p.name,m;
         */
        $productTbl = 'product';

        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select([
            self::TBL,
            'DATE_FORMAT(' . self::TBL . '.start_date, \'%m\') as m',
            'count(' . self::TBL . '.id) as c',
            $productTbl
        ])
            ->where('IDENTITY(' . self::TBL . '.company) = :company_id')
            ->andWhere('DATE_FORMAT(' . self::TBL . '.start_date, \'%Y\') = :year');

        $qb->leftJoin(self::TBL . '.product', $productTbl);
        $qb->groupBy($productTbl . '.id','m');

        $qb->setParameters([
            'company_id' => (string) $issuer->getId(),
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
        foreach ($results as $result) {
            $product = $this->getDomainModel($result[0]['product'], 'Product');
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



    public function countByIssuerProductForMonth(
        Company $issuer,
        Product $product,
        int $year,
        int $month
    ) {
        $thisMonth = $this->dateFromMonthAndYear($month, $year);
        if ($month == 12) {
            $year++;
            $month = 1;
        } else {
            $month++;
        }
        $nextMonth = $this->dateFromMonthAndYear($month, $year);
        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select('count(' . self::TBL . '.id)')
            ->where('IDENTITY(' . self::TBL . '.company) = :company_id')
            ->andWhere('IDENTITY(' . self::TBL . '.product) = :product_id')
            ->andWhere(self::TBL . '.start_date >= :this_month')
            ->andWhere(self::TBL . '.start_date < :next_month');

        $qb->setParameters([
            'company_id' => (string) $issuer->getId(),
            'product_id' => (string) $product->getId(),
            'this_month' => $thisMonth,
            'next_month' => $nextMonth
        ]);
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function sumForIssuerAndProductBetweenDates(
        Company $issuer,
        Product $product,
        DateTime $startTime,
        DateTime $endTime = null
    ) {

        if ($endTime) {

        }
    }

    private function dateFromMonthAndYear(int $month, int $year): DateTimeImmutable
    {
        $string = '1/' . $month . '/' . $year . ' 00:00:00';
        return DateTimeImmutable::createFromFormat('d/m/Y H:i:s', $string);
    }


    public function sumForProductGroupedByCurrencyForYearToDate(
        DateTimeImmutable $endDate,
        Product $product = null
    ) {
        /*
         * select DATE_FORMAT(start_date, '%m') as m, p.name, count(*)
         * from securities left join products as p on product_id = p.id
         * where company_id = 29 and DATE_FORMAT(start_date, '%Y') = "2012" group by p.name,m;
         */
        $currencyTbl = 'product';

        $year = $endDate->format('Y');

        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select([
            self::TBL,
            'sum(' . self::TBL . '.money_raised) as s',
            $currencyTbl
        ])
            ->where('DATE_FORMAT(' . self::TBL . '.start_date, \'%Y\') = :year')
            ->andWhere(self::TBL . '.start_date <= :end_date')
            ->leftJoin(self::TBL . '.currency', $currencyTbl);

        $params =[
            'year' => $year,
            'end_date' => $endDate
        ];

        if ($product) {
            $qb->andWhere('IDENTITY(' . self::TBL . '.product) = :product_id');
            $params['product_id'] = (string) $product->getId();
        }

        $qb->groupBy($currencyTbl . '.id')
            ->setParameters($params);


        /*
         * List of:
         * 0 => Security
         * c => count
         * m => month
        */
        $results = $qb->getQuery()->getArrayResult();

        $currencies = [];
        foreach ($results as $result) {
            $currency = $this->getDomainModel($result[0]['currency'], 'Currency');
            $code = $currency->getCode();
            $total = (int) $result['s'];
            $currencies[$code] = $total;
        }

        return $currencies;
    }

    public function sumForProductGroupedByCountryForYearToDate(
        DateTimeImmutable $endDate,
        Product $product = null
    ) {
        /*
         * select c.name, sum(*)
         * from securities left join products as p on product_id = p.id
         * where company_id = 29 and DATE_FORMAT(start_date, '%Y') = "2012" group by p.name,m;
         */
        $companyTbl = 'company';
        $countryTbl = 'country';

        $year = $endDate->format('Y');

        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select([
            self::TBL,
            'sum(' . self::TBL . '.money_raised) as s',
            $companyTbl,
            $countryTbl
        ])
            ->where('DATE_FORMAT(' . self::TBL . '.start_date, \'%Y\') = :year')
            ->andWhere(self::TBL . '.start_date <= :end_date')
            ->leftJoin(self::TBL . '.company', $companyTbl)
            ->leftJoin($companyTbl . '.country', $countryTbl);

        $params =[
            'year' => $year,
            'end_date' => $endDate
        ];

        if ($product) {
            $qb->andWhere('IDENTITY(' . self::TBL . '.product) = :product_id');
            $params['product_id'] = (string) $product->getId();
        }

        $qb->groupBy($countryTbl . '.id')
            ->setParameters($params);


        /*
         * List of:
         * 0 => Security
         * s => sum
        */
        $results = $qb->getQuery()->getArrayResult();

        $countries = [];
        foreach ($results as $result) {
            $country = $this->getDomainModel($result[0]['company']['country'], 'Country');
            $code = $country->getName();
            $total = (int) $result['s'];
            $countries[$code] = $total;
        }

        return $countries;
    }

    public function countsByProduct()
    {
        /*
         * select p.name, count(s.id)
         * from securities as s left join products as p on s.product_id = p.id
         * group by p.id;
         */
        $productTable = 'product';

        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select([
            self::TBL,
            'count(' . self::TBL . '.id) as c',
            $productTable
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
        foreach ($results as $result) {
            $product = $this->getDomainModel($result[0]->getProduct());
            $total = (int) $result['c'];
            $products[] = (object) [
                'product' => $product,
                'count' => $total
            ];
        }

        return $products;
    }

    public function findUpcomingMaturities(
        DateTimeImmutable $dateFrom,
        int $limit = self::DEFAULT_LIMIT
    ): ServiceResultInterface {
        $qb = $this->selectWithJoins();
        $qb->where(self::TBL . '.maturity_date > :end_from')
            ->orderBy(self::TBL . '.maturity_date', 'ASC')
            ->setMaxResults($limit)
            ->setParameter('end_from', $dateFrom);
        return $this->getServiceResult($qb);
    }

    protected function getServiceResult(QueryBuilder $qb, $type = 'Security')
    {
        return parent::getServiceResult($qb, $type);
    }
}
