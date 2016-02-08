<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\SecurityFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ReportsController extends Controller
{
    use SecurityFilter;

    public function listAction()
    {
        return $this->renderTemplate('reports:list');
    }

    public function fsa50Action(Request $request)
    {
        $products = $this->get('app.services.products')
            ->findAll()->getDomainModels();

        $product = $this->setProductFilter($request);

        $endThisYear = new \DateTimeImmutable('2015-11-20'); // @todo - based on today's date?
        $endLastYear = $endThisYear->sub(new \DateInterval('P1Y'));

        $resultsThis = $this->get('app.services.securities')->sumForProductGroupedByCountryForYearToDate(
            $endThisYear,
            $product
        );
        $resultsLast = $this->get('app.services.securities')->sumForProductGroupedByCountryForYearToDate(
            $endLastYear,
            $product
        );

        $countries = array_unique(array_merge(array_keys($resultsThis),array_keys($resultsLast)));
        $rows = [];

        foreach ($countries as $country) {
            $rows[] = [
                $country,
                $resultsThis[$country] ?? 0,
                $resultsLast[$country] ?? 0
            ];
        }

        usort($rows, function($a, $b) {
            return $b[1] <=> $a[1];
        });

        $other = array_slice($rows, 9);
        $rows = array_slice($rows, 0, 9);

        if (!empty($other)) {
            $otherRow = [
                'Other',
                0,
                0
            ];

            foreach ($other as $o) {
                $otherRow[1] += $o[1];
                $otherRow[2] += $o[2];
            }

            $rows[] = $otherRow;
        }

        $headings = [
            'Country',
            $endThisYear->format('Y') . ' (to ' . $endThisYear->format('d M') . ')',
            $endLastYear->format('Y') . ' (to ' . $endLastYear->format('d M') . ')'
        ];

        $this->toView('headings', $headings);
        $this->toView('products', $products);
        $this->toView('rows', $rows);
        $this->toView('graphData', array_merge(
            [
                $headings
            ],
            $rows
        ));
        return $this->renderTemplate('reports:fsa50');
    }

    public function fsa54Action(Request $request)
    {
        $products = $this->get('app.services.products')
            ->findAll()->getDomainModels();

        $product = $this->setProductFilter($request);

        $endThisYear = new \DateTimeImmutable('2015-11-20'); // @todo - based on today's date?
        $endLastYear = $endThisYear->sub(new \DateInterval('P1Y'));

        $resultsThis = $this->get('app.services.securities')->sumForProductGroupedByCurrencyForYearToDate(
            $endThisYear,
            $product
        );
        $resultsLast = $this->get('app.services.securities')->sumForProductGroupedByCurrencyForYearToDate(
            $endLastYear,
            $product
        );

        $currencies = array_unique(array_merge(array_keys($resultsThis),array_keys($resultsLast)));
        $rows = [];

        foreach ($currencies as $currency) {
            $rows[] = [
                $currency,
                $resultsThis[$currency] ?? 0,
                $resultsLast[$currency] ?? 0
            ];
        }

        usort($rows, function($a, $b) {
            return $b[1] <=> $a[1];
        });

        $other = array_slice($rows, 3);
        $rows = array_slice($rows, 0, 3);

        if (!empty($other)) {
            $otherRow = [
                'Other',
                0,
                0
            ];

            foreach ($other as $o) {
                $otherRow[1] += $o[1];
                $otherRow[2] += $o[2];
            }

            $rows[] = $otherRow;
        }

        $headings = [
            'Currency',
            $endThisYear->format('Y') . ' (to ' . $endThisYear->format('d M') . ')',
            $endLastYear->format('Y') . ' (to ' . $endLastYear->format('d M') . ')'
        ];

        $this->toView('headings', $headings);
        $this->toView('products', $products);
        $this->toView('rows', $rows);
        $this->toView('graphData', array_merge(
            [
                $headings
            ],
            $rows
        ));
        return $this->renderTemplate('reports:fsa54');
    }
}