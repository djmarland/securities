<?php

namespace AppBundle\Controller;

use ConsoleBundle\Command\ExchangeRatesCommand;
use DateTimeImmutable;
use SecuritiesService\Domain\Entity\Currency;
use SecuritiesService\Domain\Entity\ExchangeRate;
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
        $currency = $this->getCurrency();

        $today = $this->getApplicationTime();
        $yesterday = $today->sub(new \DateInterval('P1D'));
        $hundredDaysAgo = $today->sub(new \DateInterval('P100D'));

        $ratesService = $this->get('app.services.exchange_rates');

        /** @var ExchangeRate[] $rates */
        $rates = $ratesService->findDatesForCurrency($currency, $hundredDaysAgo, $today);
        $this->toView('hasData', false);


        if (!empty($rates)) {
            $this->toView('hasData', true);

            $count = count($rates);
            $latest = $rates[$count-1];
            $penultimate = $rates[$count-2] ?? null;

            $changeRate = null;

            $this->toView('latestRate', $latest->getValueUSD());
            $this->toView('latestDate', $latest->getDate()->format('d/m/Y'));

            if ($penultimate) {
                $changeRate = $latest->getValueUSD() - $penultimate->getValueUSD();
            }

            $this->toView('changeRate', number_format($changeRate, 9));
            $this->toView('changeDate', $penultimate->getDate()->format('d/m/Y'));


            $graphData = [];
            $graphLabels = [];

            foreach ($rates as $rate) {
                $graphLabels[] = $rate->getDate()->format('d/m/Y');
                $graphData[] = $rate->getValueUSD();
            }
            $this->toView('recentGraph', $this->makeGraph($currency->getCode(), $graphData, $graphLabels));

            $earliestRate = $ratesService->findEarliestForCurrency($currency);
            $earliestYear = (int) $earliestRate->getDate()->format('Y');
            $currentYear = (int) $today->format('Y');
            $tenYearsAgo = $currentYear - 10;

            $linkableYears = [];
            $lastDecadeDates = [];

            for ($i = $earliestYear; $i <= $currentYear; $i++) {
                $linkableYears[] = (object) [
                    'hrefParams' => [
                        'code' => $currency->getCode(),
                        'year' => $i
                    ],
                    'text' => $i,
                ];
                if ($i >= $tenYearsAgo) {
                    $lastDecadeDates[] = $i . $yesterday->format('-m-d');
                }
            }

            $this->toView('linkableYears', array_reverse($linkableYears));

            $decadeRates = $ratesService->findSpecifcDatesForCurrency($currency, $lastDecadeDates);
            $dGraphData = [];
            $dGraphLabels = [];
            foreach ($decadeRates as $rate) {
                $dGraphLabels[] = $rate->getDate()->format('d/m/Y');
                $dGraphData[] = $rate->getValueUSD();
            }
            $this->toView('decadeGraph', $this->makeGraph($currency->getCode(), $dGraphData, $dGraphLabels));
        }
        $this->toView('rates', $rates);

        return $this->renderTemplate('currencies:show', 'Currency - ' . $currency->getCode());
    }

    public function showYearAction()
    {
        $currency = $this->getCurrency();
        $year = $this->request->get('year');

        $this->toView('year', $year);

        $this->toView('hasData', false);

        $ratesService = $this->get('app.services.exchange_rates');
        $startOfYear = new DateTimeImmutable($year . '-01-01');
        $endOfYear = new DateTimeImmutable($year . '-12-31');
        $this->toView('yearGraph', null);

        /** @var ExchangeRate[] $rates */
        $rates = $ratesService->findDatesForCurrency($currency, $startOfYear, $endOfYear);
        if (!empty($rates)) {
            $this->toView('hasData', true);
            $graphData = [];
            $graphLabels = [];

            $months = [];
            $months[1] = ['name' => 'January', 'rates' => null];
            $months[2] = ['name' => 'February', 'rates' => null];
            $months[3] = ['name' => 'March', 'rates' => null];
            $months[4] = ['name' => 'April', 'rates' => null];
            $months[5] = ['name' => 'May', 'rates' => null];
            $months[6] = ['name' => 'June', 'rates' => null];
            $months[7] = ['name' => 'July', 'rates' => null];
            $months[8] = ['name' => 'August', 'rates' => null];
            $months[9] = ['name' => 'September', 'rates' => null];
            $months[10] = ['name' => 'October', 'rates' => null];
            $months[11] = ['name' => 'November', 'rates' => null];
            $months[12] = ['name' => 'December', 'rates' => null];

            foreach ($rates as $rate) {
                $date = $rate->getDate()->format('d/m/Y');
                $rateUSD = $rate->getValueUSD();
                $graphLabels[] = $date;
                $graphData[] = $rateUSD;
                $month = (int) $rate->getDate()->format('m');
                if (!isset($months[$month]['rates'])) {
                    $months[$month]['rates'] = [];
                }
                $months[$month]['rates'][] = [
                    'date' => $date,
                    'rate' => $rateUSD,
                ];
            }
            $this->toView('yearGraph', $this->makeGraph($currency->getCode(), $graphData, $graphLabels));
            $this->toView('yearMonths', $months);
        }


        return $this->renderTemplate('currencies:show-year', 'Currency - ' . $currency->getCode() . ' - ' . $year);
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

    private function getCurrency()
    {
        $code = $this->request->get('code');
        try {
            /** @var Currency $currency */
            $currency = $this->get('app.services.currencies')->findByCode($code);
        } catch (EntityNotFoundException $e) {
            throw new HttpException(404, 'Currency ' . $code . ' does not exist.');
        }

        $this->toView('currency', $currency);
        $this->toView('isBase', $currency->getCode() == 'USD');
        return $currency;
    }

    private function makeGraph($datasetLabel, $data, $labels)
    {
        return (object) [
            'type' => 'line',
            'data' => (object) [
                'labels' => $labels,
                'datasets' => [
                    (object) [
                        'fill' => true,
                        'label' => $datasetLabel,
                        'borderWidth' => 1,
                        'pointRadius' => 0,
                        'pointHitRadius' => 3,
                        'borderColor' =>'#634D7B',
                        'backgroundColor' =>'#B8B0C0',
                        'data' => $data,
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
                    "maintainAspectRatio": true,
                    "elements" : {
                        "line": {
                            "tension": 0.2
                        }
                    },
                    "animation" : {
                        "duration" : 0
                    }
                }')
        ];
    }
}
