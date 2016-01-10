<?php

namespace SecuritiesService\Service;

use DateTime;
use DateTimeImmutable;
use SecuritiesService\Domain\Entity\Company;
use SecuritiesService\Domain\Entity\Line;
use SecuritiesService\Domain\ValueObject\ISIN;

class SecuritiesService extends Service
{
    const SECURITY_ENTITY = 'Security';

    private function selectWithJoins()
    {
        $currency = 'c';
        $company = 'co';
        $line = 'line';

        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select(self::TBL, $currency, $company, $line);
        $qb->leftJoin(self::TBL . '.currency', $currency);
        $qb->leftJoin(self::TBL . '.company', $company);
        $qb->leftJoin(self::TBL . '.line', $line);
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
        $result = $qb->getQuery()->getResult();
        return $this->getServiceResult($result);
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
        $qb->andWhere(self::TBL . '.isin LIKE ?0');
        $qb->setParameters(['%' . $query . '%']);
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function search(
        string $query,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {
        $qb = $this->selectWithJoins();
        $qb->andWhere(self::TBL . '.isin LIKE :query');
        $qb->setParameters(['query' => '%' . $query . '%']);

        $qb->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));
        $result = $qb->getQuery()->getResult();
        return $this->getServiceResult($result);
    }

    public function findByIsin(ISIN $isin): ServiceResultInterface
    {
        $entity = $this->getEntity(self::SECURITY_ENTITY);

        $result = $entity->findBy(
            ['isin' => $isin]
        );

        return $this->getServiceResult($result);
    }

    public function findAndCountByIssuer(
        Company $issuer,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {
        return $this->findAndCountByIssuerAndLine($issuer, null, $limit, $page);
    }

    public function findAndCountByIssuerAndLine(
        Company $issuer,
        Line $line = null,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {

        // count them first (cheaper if zero)
        $count = $this->countByIssuerAndLine($issuer, $line);
        if ($count == 0) {
            return new ServiceResultEmpty();
        }

        // find the latest
        $result = $this->findByIssuerAndLine($issuer, $line, $limit, $page);
        $result->setTotal($count);
        return $result;
    }

    public function countByIssuer(Company $issuer): int
    {
        return $this->countByIssuerAndLine($issuer, null);
    }

    public function countByIssuerAndLine(
        Company $issuer,
        Line $line = null
    ): int {
        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select('count(' . self::TBL . '.id)')
            ->where('IDENTITY(' . self::TBL . '.company) = :company_id');
        $parameters = ['company_id' => (string) $issuer->getId()];
        if ($line) {
            $qb->andWhere('IDENTITY(' . self::TBL . '.line) = :line_id');
            $parameters['line_id'] = (string) $line->getId();
        }
        $qb->setParameters($parameters);
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findByIssuer(
        Company $issuer,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {
        return $this->findByIssuerAndLine($issuer, null, $limit, $page);
    }

    public function findByIssuerAndLine(
        Company $issuer,
        Line $line = null,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {
        $qb = $this->selectWithJoins();
        $qb->where('IDENTITY(' . self::TBL . '.company) = :company_id')
            ->orderBy(self::TBL . '.isin', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));
        $parameters = ['company_id' => (string) $issuer->getId()];
        if ($line) {
            $qb->andWhere('IDENTITY(' . self::TBL . '.line) = :line_id');
            $parameters['line_id'] = (string) $line->getId();
        }
        $qb->setParameters($parameters);
        $result = $qb->getQuery()->getResult();
        return $this->getServiceResult($result);
    }

    public function sumByIssuer(
        Company $issuer
    ): int {
        return $this->sumByIssuerAndLine($issuer, null);
    }

    public function sumByIssuerAndLine(
        Company $issuer,
        Line $line = null
    ): int {
        $qb = $this->getQueryBuilder(self::SECURITY_ENTITY);
        $qb->select('sum(' . self::TBL . '.money_raised)')
            ->where('IDENTITY(' . self::TBL . '.company) = :company_id');
        $parameters = ['company_id' => (string) $issuer->getId()];
        if ($line) {
            $qb->andWhere('IDENTITY(' . self::TBL . '.line) = :line_id');
            $parameters['line_id'] = (string) $line->getId();
        }
        $qb->setParameters($parameters);
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function countByIssuerLineForMonth(
        Company $issuer,
        Line $line,
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
            ->andWhere('IDENTITY(' . self::TBL . '.line) = :line_id')
            ->andWhere(self::TBL . '.start_date >= :this_month')
            ->andWhere(self::TBL . '.start_date < :next_month');

        $qb->setParameters([
            'company_id' => (string) $issuer->getId(),
            'line_id' => (string) $line->getId(),
            'this_month' => $thisMonth,
            'next_month' => $nextMonth
        ]);
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function sumForIssuerAndLineBetweenDates(
        Company $issuer,
        Line $line,
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
}
