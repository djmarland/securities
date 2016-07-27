<?php

namespace AppBundle\Controller;

use ConsoleBundle\Command\ExchangeRatesCommand;
use SecuritiesService\Domain\Entity\Currency;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CurrenciesController extends Controller
{
    use Traits\CurrenciesTableTrait;

    public function indexAction()
    {
        $this->buildCurrenciesTable();
        return $this->renderTemplate('currencies:index', 'Currencies');
    }

    public function showAction()
    {
        $code = $this->request->get('code');

        try {
            /** @var Currency $currency */
            $currency = $this->get('app.services.currencies')->findByCode($code);
        } catch (EntityNotFoundException $e) {
            throw new HttpException(404, 'Currency ' . $code . ' does not exist.');
        }

        $rates = $this->get('app.services.exchange_rates')
            ->findAllForCurrency($currency);
        $this->toView('hasData', false);

        if (!empty($rates)) {
            $this->toView('hasData', true);

            $graphData = [];
            $graphLabels = [];

            foreach (array_reverse($rates) as $rate) {
                $graphLabels[] = $rate->getDate()->format('d/m/Y');
                $graphData[] = $rate->getValueUSD();
            }

            $graphSettings = (object) [
                'type' => 'line',
                'data' => (object) [
                    'labels' => $graphLabels,
                    'datasets' => [
                        (object) [
                            'fill' => false,
                            'label' => $currency->getCode(),
                            'borderWidth' => 6,
                            'borderColor' =>'#634D7B',
                            'backgroundColor' =>'#634D7B',
                            'data' => $graphData,
                        ],
                    ],
                ],
                'options' => json_decode('{
                    "axisFormat" : "$#",
                    "tooltips"  : {
                        "callbacks" : {}
                    },
                    "scales" : {
                        "yAxes" : [{
                            "ticks": {}
                        }]
                    },
                    "legend" : {
                        "display": false
                    },
                    "responsive": true,
                    "hover" : {
                        "mode" : "label"
                    },
                    "maintainAspectRatio": false,
                    "elements" : {
                        "line": {
                            "tension": 0.2
                        }
                    }
                }')
            ];


            $this->toView('graphSettings', $graphSettings);
        }

        $this->toView('currency', $currency);
        $this->toView('rates', $rates);

        return $this->renderTemplate('currencies:show', 'Currency - ' . $currency->getCode());
    }

    public function updateAction()
    {
        $fromDate = $this->request->get('fromDate');
        $toDate = $this->request->get('toDate');

        $command = new ExchangeRatesCommand();
        $command->setContainer($this->container);
        $input = new ArrayInput([
            'dateFrom' => $fromDate,
            'dateTo' => $toDate
        ]);
        $output = new BufferedOutput();

        $command->run($input, $output);
        $content = $output->fetch();

        $lines = explode("\n", trim($content));
        $this->toView('messages', $lines, true);
        return $this->renderTemplate('json');
    }
}
