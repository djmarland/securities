<?php

namespace AppBundle\Presenter\Organism\Issuance;

use AppBundle\Presenter\Molecule\Money\MoneyPresenter;

class IssuanceTablePresenter extends Issuance implements IssuanceTablePresenterInterface
{
    public function getHeadings()
    {
        return array_merge(
            ['Month'],
            array_keys($this->results)
        );
    }

    public function getRows()
    {
        $rows = [];
        $cumulative = [];
        foreach ($this->getMonths() as $monthNum => $monthName) {
            $row = [
                [
                    'element' => 'th',
                    'link' => null,
                    'text' => $monthName,
                    'presenter' => null,
                ],
            ];

            foreach ($this->results as $year => $months) {
                $col = [
                    'element' => 'td',
                    'link' => null,
                    'text' => '-',
                    'presenter' => null,
                ];

                if (!isset($cumulative[$year])) {
                    $cumulative[$year] = 0;
                }

                $value = null;

                if (isset($months[$monthNum]) && !$this->options['cumulative']) {
                    $link = [
                        'params' => [
                            'issueDate' => $year . '-' . str_pad($monthNum, 2, '0', STR_PAD_LEFT),
                        ],
                    ];

                    if ($this->domainModel) {
                        $link['path'] = $this->domainModel->getRoutePrefix() . '_securities';
                        $link['params'][$this->domainModel->getRoutePrefix() . '_id'] = (string) $this->domainModel->getId();
                    } else {
                        $link['path'] = 'overview_securities';
                    }

                    $col['link'] = $link;
                }

                if (isset($months[$monthNum])) {
                    $cumulative[$year] = $cumulative[$year] + $months[$monthNum];
                    $value = $months[$monthNum];
                }

                if ($this->options['cumulative'] &&
                    $this->monthIsNotFuture($year, $monthNum)) {
                    $value = $cumulative[$year];
                }

                if ($value) {
                    $col['presenter'] = new MoneyPresenter($value, [
                        'scale' => true,
                        'currency' => '$'
                    ]);
                }

                $row[] = $col;
            }

            $rows[] = $row;
        }
        return $rows;
    }
}
