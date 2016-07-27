<?php

namespace SecuritiesService\Domain\Entity;

use DateTimeImmutable;
use SecuritiesService\Domain\Exception\DataNotSetException;
use SecuritiesService\Domain\ValueObject\UUID;

class ExchangeRate extends Entity
{
    private $rate;

    private $date;

    private $currency;

    public function __construct(
        UUID $id,
        float $rate,
        DateTimeImmutable $date,
        Currency $currency = null
    ) {
        parent::__construct($id);

        $this->rate = $rate;
        $this->date = $date;
        $this->currency = $currency;
    }

    public function getValue(): float
    {
        return $this->rate;
    }

    public function getValueUSD(): float
    {
        return 1/$this->rate;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getCurrency(): Currency
    {
        if (!$this->currency) {
            throw new DataNotSetException('Currency for ExchangeRate was not fetched');
        }
        return $this->currency;
    }
}
