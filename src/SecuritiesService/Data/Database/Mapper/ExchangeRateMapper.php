<?php

namespace SecuritiesService\Data\Database\Mapper;

use DateTimeImmutable;
use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\ExchangeRate;
use SecuritiesService\Domain\ValueObject\UUID;

class ExchangeRateMapper extends Mapper
{
    public function getDomainModel(array $item): Entity
    {
        $id = new UUID($item['id']);
        $currency = null;

        if (isset($item['currency'])) {
            $currency = $this->mapperFactory->createCurrency()->getDomainModel($item['currency']);
        }

        $exchangeRate = new ExchangeRate(
            $id,
            $item['rate'],
            DateTimeImmutable::createFromMutable($item['date']),
            $currency
        );
        return $exchangeRate;
    }
}
