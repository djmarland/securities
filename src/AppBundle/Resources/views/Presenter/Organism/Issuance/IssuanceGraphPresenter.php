<?php

namespace AppBundle\Presenter\Organism\Issuance;

use SecuritiesService\Domain\Entity\Entity;

class IssuanceGraphPresenter extends Issuance implements IssuanceGraphPresenterInterface
{
    private $axisFormat = '£#m';
    private $scale = 1;

    public function __construct(
        Entity $entity = null,
        array $results = [],
        array $options = []
    ) {
        parent::__construct($entity, $results, $options);
        $this->calculateScale();
    }

    public function getData()
    {
        $years = array_keys($this->results);
        $cumulative = [];

        // create the heading types
        $graphData = [
            array_map(function ($year) {
                return (string) $year;
            }, $years),
        ];
        // lead with Month heading
        array_unshift($graphData[0], 'Month');

        $months = $this->getMonths();
        foreach ($months as $monthNum => $monthName) {
            // for each month, set the count for each product
            $row = [$monthName];
            foreach ($years as $year) {
                if (!isset($cumulative[$year])) {
                    $cumulative[$year] = 0;
                }

                $val = $this->results[$year][$monthNum] ?? 0;

                $cumulative[$year] = $cumulative[$year] + $val;

                if ($this->options['cumulative']) {
                    if ($this->monthIsNotFuture($year, $monthNum)) {
                        $val = $cumulative[$year];
                    } else {
                        $val = null;
                    }
                }

                if (!is_null($val)) {
                    $row[] = (float)($val / $this->scale);
                } else {
                    $row[] = null;
                }
            }
            $graphData[] = $row;
        }
        return $graphData;
    }

    public function getChartOptions()
    {
        $options = (object) [
            'height' => 400,
            'legend' => (object) [
                'position' => 'right',
            ],
            'backgroundColor' => 'transparent',
            'animation' => (object) [
                'duration' => 400,
                'startup' => true,
            ],
            'vAxis' => (object) [
                'format' => $this->axisFormat
            ],
            'bar' => (object) [
                'groupWidth' => '82%',
            ],
            'lineWidth' => 6,
            'pointsVisible' => true,
            'pointSize' => 12,
            'chartArea' => (object) [
                'left' => 72,
                'top' => 24,
                'height' => 352,
                'width' => '75%',
            ],
            'axisTitlesPosition' => 'in',
            'colors' => [
                '#E6E0EE', '#B5A2CC', '#3A0676'
            ],
        ];

        return $options;
    }

    public function getChartType()
    {
        return $this->options['cumulative'] ? 'LineChart' : 'ColumnChart';
    }

    private function calculateScale()
    {
        $largest = 0;
        // todo - handle cumulative

        foreach ($this->results as $year) {
            foreach ($year as $monthValue) {
                if ($monthValue > $largest) {
                    $largest = $monthValue;
                }
            }
        }

        // half the largest, so that it only kicks in
        // for greater than 2bn or 2tr
//        $largest = $largest / 2;
        
        // todo - share this logic with MoneyPresenter
        if ($largest > 1000000) { // trillions
            $this->scale = 1000000;
            $this->axisFormat = '£#tr';
        } elseif ($largest > 1000) { // billions
            $this->scale = 1000;
            $this->axisFormat = '£#bn';
        }
    }
}
