<?php

namespace AppBundle\Presenter\Organism\Issuance;

class IssuanceGraphPresenter extends Issuance implements IssuanceGraphPresenterInterface
{
    public function getData()
    {
        $years = array_keys($this->results);

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
                $val = $this->results[$year][$monthNum] ?? 0;
                $row[] = (float) $val;
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
                'format' => '£#m'
            ],
            'bar' => (object) [
                'groupWidth' => '82%',
            ],
            'chartArea' => (object) [
                'left' => 72,
                'top' => 24,
                'height' => 352,
                'width' => '75%',
            ],
            'axisTitlesPosition' => 'in',
            'colors' => [
                '#3A0676', '#E6E0EE'
            ],
        ];

        return $options;
    }
}
