<?php

namespace AppBundle\Presenter\Organism\ExchangeRate;

use AppBundle\Presenter\Presenter;
use SecuritiesService\Domain\Entity\ExchangeRate;

class ExchangeRatePresenter extends Presenter implements ExchangeRatePresenterInterface
{
    /**
     * @var ExchangeRate
     */
    private $exchangeRate;

    public function __construct($domainModel, array $options = [])
    {
        parent::__construct($domainModel, $options);
        $this->exchangeRate = $domainModel;
    }

    public function getCode(): string
    {
        return $this->exchangeRate->getCurrency()->getCode();
    }

    public function getValue(): string
    {
        return $this->number($this->exchangeRate->getValue());
    }

    public function getValueUSD(): string
    {
        return $this->number($this->exchangeRate->getValueUSD());
    }

    public function getValueSetDate(): string
    {
        return $this->exchangeRate->getDate()->format('d/m/Y');
    }

    public function getPathName(): string
    {
        return 'currencies_show';
    }

    public function getPathParams(): array
    {
        return ['code' => $this->exchangeRate->getCurrency()->getCode()];
    }

    private function number($value)
    {
       return rtrim(number_format($value, 6), '0.');
    }
}
