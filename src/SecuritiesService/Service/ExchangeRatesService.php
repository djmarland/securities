<?php

namespace SecuritiesService\Service;

use DateTimeImmutable;
use Doctrine\ORM\Query\Expr\Join;
use SecuritiesService\Data\Database\Entity\ExchangeRate as DbExchangeRate;
use SecuritiesService\Domain\Entity\Currency;
use SecuritiesService\Domain\Entity\ExchangeRate;
use SecuritiesService\Domain\Exception\EntityNotFoundException;

class ExchangeRatesService extends Service
{
    const SERVICE_ENTITY = 'ExchangeRate';

    public function countAll(): int
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('count(' . self::TBL . '.id)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findLatestForAllCurrencies(): array
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL, 'currency')
            ->leftJoin(
                DbExchangeRate::class,
                'ex',
                Join::WITH,
                $qb->expr()->andX(
                    self::TBL . '.currency = ex.currency',
                    self::TBL . '.date < ex.date'
                )
            )
            ->join(self::TBL . '.currency', 'currency')
            ->where('ex.date IS NULL')
            ->orderBy('currency.code', 'ASC');

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function findAllForCurrency(Currency $currency): array
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL)
            ->where(self::TBL . '.currency = :currencyId')
            ->orderBy(self::TBL . '.date', 'DESC')
            ->setParameter('currencyId', $currency->getId()->getBinary());

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function findDatesForCurrency(
        Currency $currency,
        DateTimeImmutable $fromDate,
        DateTimeImmutable $toDate
    ): array {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL)
            ->where(self::TBL . '.currency = :currencyId')
            ->andWhere(self::TBL . '.date >= :from')
            ->andWhere(self::TBL . '.date <= :to')
            ->orderBy(self::TBL . '.date', 'ASC')
            ->setParameter('currencyId', $currency->getId()->getBinary())
            ->setParameter('from', $fromDate)
            ->setParameter('to', $toDate);

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function findSpecifcDatesForCurrency(
        Currency $currency,
        array $dateTimes
    ): array {
        $dates = array_map(function($date) {
            return $date->format('Y-m-d');
        }, $dateTimes);

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL)
            ->where(self::TBL . '.currency = :currencyId')
            ->andWhere(self::TBL . '.date IN (:dates)')
            ->orderBy(self::TBL . '.date', 'ASC')
            ->setParameter('currencyId', $currency->getId()->getBinary())
            ->setParameter('dates', $dates);

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function findEarliestForCurrency(Currency $currency): ExchangeRate
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL)
            ->where(self::TBL . '.currency = :currencyId')
            ->orderBy(self::TBL . '.date', 'ASC')
            ->setParameter('currencyId', $currency->getId()->getBinary())
            ->setMaxResults(1);

        $results = $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
        if (empty($results)) {
            throw new EntityNotFoundException('No data for this currency ' . $currency->getCode());
        }
        return reset($results);
    }
}
