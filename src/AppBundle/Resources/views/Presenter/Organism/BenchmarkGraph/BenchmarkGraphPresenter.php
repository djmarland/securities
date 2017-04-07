<?php

namespace AppBundle\Presenter\Organism\BenchmarkGraph;

use AppBundle\Presenter\Presenter;
use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\Security;

class BenchmarkGraphPresenter extends Presenter
{
    private $axisFormat = '£#m';
    private $scale = 1;

    private $securities;
    private $securitiesByCurrency;
    private $biggestValue;

    public function __construct(
        array $securities = [],
        array $options = []
    ) {
        parent::__construct(null, $options);
        $this->securities = $securities;
        $this->securitiesByCurrency = $this->securitiesByCurrency($securities);
        $this->biggestValue = $this->biggestValue($securities);
    }

    private function biggestValue($securities) {
        $value = 0;
        foreach ($securities as $security) {
            /** @var Security $security */
            if ($security->getMoneyRaisedUSD() > $value) {
                $value = $security->getMoneyRaisedUSD();
            }
        }
        return $value;
    }

    public function securitiesByCurrency($securities)
    {
        $currencies = [];
        foreach ($securities as $security) {
            /** @var Security $security */
            $code = $security->getCurrency()->getCode();
            if (!isset($currencies[$code])) {
                $currencies[$code] = [];
            }
            $currencies[$code][] = $security;
        }
        return $currencies;
    }

    public function getSettings()
    {
        return (object) [
            'type' => 'bubble',
            'data' => [
                'datasets' => $this->getDatasets(),
            ],
            'options' => json_decode('{
                "tooltips"  : {
                    "callbacks" : {}
                },
                "scales" : {
                    "yAxes" : [{
                        "ticks": {
                            "beginAtZero": true
                        }
                    }],
                    "xAxes" : [{
                        "ticks": {
                            "beginAtZero": true
                        }
                    }]
                },
                "responsive": true,
                "hover" : {
                    "mode" : "label"
                },
                "maintainAspectRatio": true
            }')
        ];
    }

    public function getDatasets()
    {
        $datasets = [];
        $colors = ["#634D7B", "#B66D6D", "#B6B16D", "#579157", '#777', "#342638"];

        $i = 0;
        foreach ($this->securitiesByCurrency as $currency => $securities) {
            $dataset = [
                'label' => $currency,
                'data' => [],
                'backgroundColor' => $colors[$i%(count($colors))]
            ];
            foreach ($securities as $security) {
                /** @var Security $security */
                if (!$security->getMoneyRaisedUSD()) {
                    continue;
                }

                $dataset['data'][] = [
                    'x' => $security->getTerm(),
                    'y' => $security->getCoupon(),
                    'r' => $this->getRadius($security->getMoneyRaisedUSD())
                ];
            }
            $datasets[] = $dataset;
            $i++;
        }
        return $datasets;
    }

    private function getRadius($value) {
        $biggestRadius = 64;
        $ratio = $value / $this->biggestValue;
        return $ratio * $biggestRadius;
    }
}
