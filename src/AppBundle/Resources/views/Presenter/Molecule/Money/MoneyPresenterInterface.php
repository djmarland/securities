<?php

namespace AppBundle\Presenter\Molecule\Money;

interface MoneyPresenterInterface
{
    public function getValueGBP(): string;
    public function getValueIssued(): string;
    public function getIssueCurrency(): string;
    public function getDisplay(): string;
}