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
        if (is_null($value)) {
            return null;
        }
        // find the position of the first non-zero after the decimal place
        $string = number_format($value,30);
        $found = null;
        $start = strpos($string, '.') + 1;
        $position = $start;
        if ($position > 0) {
            while ($found === null && isset($string[$position])) {
                if ($string[$position] !== '0') {
                    $found = ($position - $start) + 3;
                }
                $position++;
            }
        }
        $round = $found ?? 12;
        $number = rtrim(number_format($value, $round), '0.');
        if ($number === '') {
            return '0.00';
        }
        return $number;
    }
}
