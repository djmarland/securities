<?php

namespace AppBundle\Presenter\Organism\Issuance;

class IssuanceGraphPresenter extends Issuance implements IssuanceGraphPresenterInterface
{
    public function getData()
    {
        $products = $this->resultsByProduct();

        // create the heading types
        $graphData = [
            array_map(function ($productResult) {
                return $productResult['product']->getName();
            }, $products),
        ];
        // lead with Month heading
        array_unshift($graphData[0], 'Month');

        $months = $this->getMonths();
        foreach ($months as $monthNum => $monthName) {
            // for each month, set the count for each product
            $row = [$monthName];
            foreach ($products as $product) {
                $row[] = $product['months'][$monthNum] ?? 0;
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
            'isStacked' => true,
            'animation' => (object) [
                'duration' => 400,
                'startup' => true,
            ],
            'chartArea' => (object) [
                'left' => 32,
                'top' => 24,
                'height' => 352,
                'width' => '75%',
            ],
            'axisTitlesPosition' => 'in',
            'colors' => [
                '#3A0676', '#E6E0EE', '#532587', '#6B4498', '#8463A9', '#9D83BB', '#B5A2CC', '#CEC1DD',
            ],
        ];

        return $options;
    }
}
