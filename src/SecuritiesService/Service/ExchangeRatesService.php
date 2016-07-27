<?php

namespace SecuritiesService\Service;

use Doctrine\ORM\Query\Expr\Join;
use SecuritiesService\Data\Database\Entity\ExchangeRate;
use SecuritiesService\Domain\Entity\Currency;

class ExchangeRatesService extends Service
{
    const SERVICE_ENTITY = 'ExchangeRate';

    public function findLatestForAllCurrencies(): array
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL, 'currency')
            ->join(
                ExchangeRate::class,
                'ex',
                Join::WITH,
                $qb->expr()->andX(
                    self::TBL . '.currency = ex.currency',
                    self::TBL . '.date > ex.date'
                )
            )
            ->join(self::TBL . '.currency', 'currency')
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
}
