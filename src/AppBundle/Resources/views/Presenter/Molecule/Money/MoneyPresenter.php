<?php

namespace AppBundle\Presenter\Molecule\Money;

use AppBundle\Presenter\Presenter;

class MoneyPresenter extends Presenter implements MoneyPresenterInterface
{
    /**
     * Amount in millions
     * @var float
     */
    private $amount;

    private $suffix = 'm';
    private $decimalPlaces = 2;

    protected $options = [
        'scale' => false,
        'currency' => 'GBP',
    ];

    public function __construct(
        float $amount,
        array $options = []
    )
    {
        parent::__construct(null, $options);
        $this->amount = $amount;
    }

    public function getValueGBP(): string
    {
        if ($this->isGBP()) {
            return (string) $this->getAmount();
        }
        return '';
    }

    public function getValueIssued(): string
    {
        if (!$this->isGBP()) {
            return (string) $this->getAmount();
        }
        return '';
    }

    public function getIssueCurrency(): string
    {
        return $this->options['currency'];
    }

    public function getDisplay(): string
    {
        $amount = $this->getAmount();
        if (!$amount) {
            return '';
        }

        $val = $this->scaleAmount($amount);

        // todo - trim trailing zeros
        $val = number_format($val, $this->decimalPlaces);
        return $this->getPrefix() . $val . $this->suffix;
    }

    private function getAmount()
    {
        if (0 == $this->amount) {
            return null;
        }
        return $this->amount;
    }

    private function isGBP(): bool
    {
        return ('GBP' == $this->options['currency']);
    }

    private function getPrefix(): string
    {
        if ($this->isGBP()) {
            return '£';
        }
        return $this->options['currency'];
    }

    private function scaleAmount($amount)
    {
        if ($this->options['scale']) {
            if ($amount > 1000000) { // trillions
                $amount = $amount / 1000000;
                $this->suffix = 'tr';
            } elseif ($amount > 1000) { // billions
                $amount = $amount / 1000;
                $this->suffix = 'bn';
            }
        }
        return $amount;
    }
}
