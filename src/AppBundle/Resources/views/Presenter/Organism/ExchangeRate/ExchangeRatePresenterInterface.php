<?php

namespace AppBundle\Presenter\Organism\ExchangeRate;


interface ExchangeRatePresenterInterface
{
    public function getCode(): string;
    public function getValue(): string;
    public function getValueUSD(): string;
    public function getValueSetDate(): string;
    public function getPathName(): string;
    public function getPathParams(): array;
}