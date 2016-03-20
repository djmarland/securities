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

                if (isset($months[$monthNum])) {
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
                    $col['presenter'] = new MoneyPresenter($months[$monthNum]);
                }

                $row[] = $col;
            }

            $rows[] = $row;
        }
        return $rows;
    }
}
