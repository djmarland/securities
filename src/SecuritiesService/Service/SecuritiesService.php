<?php

namespace SecuritiesService\Service;

use DateTimeImmutable;
use Doctrine\ORM\QueryBuilder;
use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\Security;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\ValueObject\Bucket;
use SecuritiesService\Domain\ValueObject\ISIN;
use SecuritiesService\Service\Filter\SecuritiesFilter;

class SecuritiesService extends Service
{
    const SERVICE_ENTITY = 'Security';

    protected $domainEntity;

    public function setDomainEntity(Entity $domainEntity)
    {
        $this->domainEntity = $domainEntity;
    }

    /* Individual */
    public function fetchByIsin(ISIN $isin): Security
    {
        $currency = 'c';
        $company = 'co';
        $product = 'p';
        $country = 'cou';
        $group = 'g';
        $sector = 's';
        $industry = 'i';

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL, $currency, $company, $product, $country, $group, $sector, $industry);
        $qb->leftJoin(self::TBL . '.currency', $currency);
        $qb->leftJoin(self::TBL . '.company', $company);
        $qb->leftJoin(self::TBL . '.product', $product);
        $qb->leftJoin($company . '.country', $country);
        $qb->leftJoin($company . '.parentGroup', $group);
        $qb->leftJoin($group . '.sector', $sector);
        $qb->leftJoin($sector . '.industry', $industry);

        $qb->where(self::TBL . '.isin = :isin')
            ->setParameter('isin', (string) $isin);

        $results = $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
        if (empty($results)) {
            throw new EntityNotFoundException();
        }

        return reset($results);
    }

    public function findAllSimple(): array
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL);
        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function find(
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        SecuritiesFilter $filter = null
    ): array {
        $qb = $this->selectWithJoins();
        $qb = $this->where($qb);
        return $this->buildFind($qb, $limit, $page, $filter);
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

    public function findAllWithoutIssuer(): array
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL)
            ->where(self::TBL . '.company IS NULL')
            ->orderBy(self::TBL . '.isin', 'ASC');

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function findNextMaturing(
        int $limit = self::DEFAULT_LIMIT
    ): array {
        $qb = $this->selectWithJoins();
        $qb = $this->where($qb);
        return $this->buildNextMaturing($qb, $limit);
    }

    public function findLatestIssuance(
        int $limit = self::DEFAULT_LIMIT
    ): array {
        $qb = $this->selectWithJoins();
        $qb = $this->where($qb);
        $qb = $this->order($qb);
        $qb->setMaxResults($limit);
        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function count(
        SecuritiesFilter $filter = null
    ): int {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('count(' . self::TBL . '.id)');
        $qb = $this->where($qb);
        if ($filter) {
            $qb = $filter->apply($qb, self::TBL);
        }
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function countAll(): int
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('count(' . self::TBL . '.id)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function countMatured(): int
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('count(' . self::TBL . '.id)');
        $qb
            ->where(self::TBL . '.maturityDate < :now')
            ->setParameter('now', $this->appTimeProvider);
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

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

    public function sum(
        SecuritiesFilter $filter = null
    ): float {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('sum(' . self::TBL . '.moneyRaised)');
        $qb = $this->where($qb);
        if ($filter) {
            $qb = $filter->apply($qb, self::TBL);
        }
        return (float) $qb->getQuery()->getSingleScalarResult();
    }

    public function sumByMonthForYear(
        int $year
    ): array {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb = $this->where($qb);
        return $this->buildSumByMonthForYear($qb, $year);
    }

    public function sumByProductForBucket(
        Bucket $bucket
    ) {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        return $this->buildSumByProductForBucket($qb, $bucket);
    }

    protected function buildFind(
        QueryBuilder $qb,
        $limit,
        $page,
        $filter = null
    ) {
        if ($filter) {
            $qb = $filter->apply($qb, self::TBL);
        }
        $qb = $this->order($qb);
        $qb = $this->paginate($qb, $limit, $page);
        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    protected function buildNextMaturing(
        QueryBuilder $qb,
        int $limit
    ) {
        $qb = $this->orderByMaturing($qb);
        $qb->setMaxResults($limit);

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    protected function buildSumByProductForBucket(
        QueryBuilder $qb,
        Bucket $bucket
    ) {

        /*
         * select p.name, count(s.id)
         * from securities as s left join products as p on s.product_id = p.id
         * group by p.id;
         */
        $productTable = 'product';

        $qb->select([
            self::TBL,
            'sum(' . self::TBL . '.moneyRaised) as total',
             $productTable,
        ])
            ->leftJoin(self::TBL . '.product', $productTable);

        $filter = new SecuritiesFilter($bucket);
        $qb = $filter->apply($qb, self::TBL);
        $qb->groupBy($productTable . '.id');

        /*
         * List of:
         * 0 => Security
         * total => sum
        */
        $results = $qb->getQuery()->getArrayResult();

        $products = [];
        $mapper = $this->mapperFactory->createMapper('Product');
        foreach ($results as $result) {
            $product = $mapper->getDomainModel($result[0]['product']);
            $total = (int) $result['total'];
            $products[] = (object) [
                'product' => $product,
                'total' => $total,
            ];
        }

        return $products;
    }

    protected function buildSumByMonthForYear(
        QueryBuilder $qb,
        int $year
    ): array {
        $qb->select([
            self::TBL,
            'DATE_FORMAT(' . self::TBL . '.startDate, \'%m\') as m',
            'sum(' . self::TBL . '.moneyRaised) as totalSum',
        ])
            ->andWhere('DATE_FORMAT(' . self::TBL . '.startDate, \'%Y\') = :year');
        $qb->groupBy('m');
        $qb->setParameter('year', (string) $year);
        /*
         * List of:
         * 0 => Security
         * totalSum => sum
         * m => month
        */
        $results = $qb->getQuery()->getArrayResult();
        $months = [];
        foreach ($results as $result) {
            $sum = $result['totalSum'];
            $month = (int) $result['m'];
            if (!isset($months[$month])) {
                $months[$month] = 0;
            }
            $months[$month] = $sum;
        }
        return $months;
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

    protected function order(
        QueryBuilder $qb
    ): QueryBuilder {
        return $qb->orderBy(self::TBL . '.startDate', 'DESC');
    }

    protected function orderByMaturing(
        QueryBuilder $qb
    ): QueryBuilder {
        return $qb->orderBy(
            'IFNULL(' . self::TBL . '.maturityDate, ' . self::TBL . '.isin), ' . self::TBL . '.isin'
        );
    }

    protected function getDomainEntity(): Entity
    {
        if ($this->domainEntity) {
            return $this->domainEntity;
        }
        throw new \InvalidArgumentException('Entity was not set, so cannot filter correctly');
    }

    protected function filterExpired(
        QueryBuilder $qb
    ): QueryBuilder {
        return $qb
            ->andWhere('(' . self::TBL . '.maturityDate > :now OR ' . self::TBL . '.maturityDate IS NULL)')
            ->setParameter('now', $this->appTimeProvider);
    }

    private function where(
        QueryBuilder $qb
    ): QueryBuilder {
        return $this->filterExpired($qb);
    }
}
