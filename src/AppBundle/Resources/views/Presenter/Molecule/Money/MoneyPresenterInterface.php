<?php

namespace AppBundle\Presenter\Molecule\Money;

interface MoneyPresenterInterface
{
    public function getValue(): string;
    public function getDisplay(): string;
}