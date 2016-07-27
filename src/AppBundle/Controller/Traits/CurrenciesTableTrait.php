<?php

namespace AppBundle\Controller\Traits;

use AppBundle\Presenter\Organism\ExchangeRate\ExchangeRatePresenter;

trait CurrenciesTableTrait
{
    private function buildCurrenciesTable()
    {
        $currenciesService = $this->get('app.services.exchange_rates');

        $rates = $currenciesService->findLatestForAllCurrencies();

        $presenters = [];
        foreach ($rates as $rate) {
            $presenters[] = new ExchangeRatePresenter($rate);
        }

        $this->toView('rates', $presenters);
    }
}




