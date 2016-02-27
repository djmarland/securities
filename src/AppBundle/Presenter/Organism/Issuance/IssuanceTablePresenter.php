<?php

namespace AppBundle\Presenter\Organism\Issuance;

use SecuritiesService\Domain\Entity\Company;
use SecuritiesService\Domain\Entity\Industry;
use SecuritiesService\Domain\Entity\ParentGroup;
use SecuritiesService\Domain\Entity\Sector;

class IssuanceTablePresenter extends Issuance implements IssuanceTablePresenterInterface
{
    public function getHeadings()
    {
        return array_values($this->getMonths());
    }

    public function getRows()
    {
        $products = $this->resultsByProduct();
        if (empty($products)) {
            return [];
        }

        $rows = [];
        foreach ($products as $productResult) {
            // create the row, with the first columns
            $row = [
                [
                    'element' => 'th',
                    'link' => null,
                    'text' => $productResult['product']->getName(),
                ],
            ];
            // create the remaining columns, for the months
            foreach (array_keys($this->getMonths()) as $monthNum) {
                $link = null;
                $text = '-';

                if (isset($productResult['months'][$monthNum])) {
                    $link = [
                        'path' => $this->routeType() . '_securities',
                        'params' => [
                            $this->routeType() . '_id' => (string) $this->domainModel->getId(),
                            'issueDate' => $this->year . '-' . str_pad($monthNum, 2, '0', STR_PAD_LEFT),
                            'product' => (string) $productResult['product']->getNumber(),
                        ],
                    ];
                    $text = $productResult['months'][$monthNum];
                }

                $row[] = [
                    'element' => 'td',
                    'link' => $link,
                    'text' => $text,
                ];
            }
            $rows[] = $row;
        }
        return $rows;
    }

    private function routeType()
    {
        // @todo - abstract this - somewhere
        if ($this->domainModel instanceof Company) {
            return 'issuer';
        }
        if ($this->domainModel instanceof ParentGroup) {
            return 'group';
        }
        if ($this->domainModel instanceof Sector) {
            return 'sector';
        }
        if ($this->domainModel instanceof Industry) {
            return 'industry';
        }
        return 'x';
    }
}
