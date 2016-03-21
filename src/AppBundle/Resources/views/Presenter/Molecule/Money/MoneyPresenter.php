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
    ];

    public function __construct(
        float $amount,
        array $options = []
    )
    {
        parent::__construct(null, $options);
        $this->amount = $amount;
    }

    public function getValue(): string
    {
        return (string) $this->amount;
    }

    public function getDisplay(): string
    {
        $val = $this->scaleAmount($this->amount);

        // todo - trim trailing zeros
        $val = number_format($val, $this->decimalPlaces);
        return '£' . $val . $this->suffix;
    }

    private function scaleAmount($amount)
    {
        if ($this->options['scale']) {
            if ($amount > 1000000) { // trillions
                $amount = $amount / 1000000;
                $this->suffix = 't';
            } elseif ($amount > 1000) { // billions
                $amount = $amount / 1000;
                $this->suffix = 'b';
            }
        }
        return $amount;
    }
}