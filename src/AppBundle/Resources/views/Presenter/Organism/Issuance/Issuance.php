<?php

namespace AppBundle\Presenter\Organism\Issuance;

use AppBundle\Presenter\Presenter;
use SecuritiesService\Domain\Entity\Entity;

abstract class Issuance extends Presenter
{
    private $productCounts;

    protected $year;

    public function __construct(
        Entity $entity,
        array $productCounts,
        int $year,
        array $options = []
    ) {
        parent::__construct($entity, $options);
        $this->productCounts = $productCounts;
        $this->year = $year;
    }

    protected function getMonths()
    {
        return [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];
    }

    protected function resultsByProduct()
    {
        $productResults = [];
        // extract the products from the results
        foreach ($this->productCounts as $monthNum => $products) {
            foreach ($products as $productID => $data) {
                if (!isset($productResults[$productID])) {
                    $productResults[$productID] = [
                        'product' => $data->product,
                        'months' => [],
                    ];
                }
                $productResults[$productID]['months'][$monthNum] = $data->total;
            }
        }

        $values = array_values($productResults);
        usort($values, function ($a, $b) {
            return strcmp($a['product']->getName(), $b['product']->getName());
        });
        return $values;
    }
}
