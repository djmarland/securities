<?php

namespace AppBundle\Controller;

class ReportsController extends Controller
{
    public function listAction()
    {
        return $this->renderTemplate('reports:list');
    }

    public function fsa54Action()
    {

        $endThisYear = new \DateTimeImmutable('2015-11-20'); // @todo - based on today's date?
        $endLastYear = $endThisYear->sub(new \DateInterval('P1Y'));

        $resultsThis = $this->get('app.services.securities')->sumForProductGroupedByCurrencyForYearToDate(
            $endThisYear
        );
        $resultsLast = $this->get('app.services.securities')->sumForProductGroupedByCurrencyForYearToDate(
            $endLastYear
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

        $headings = [
            'Currency',
            $endThisYear->format('Y') . ' (to ' . $endThisYear->format('d M') . ')',
            $endLastYear->format('Y') . ' (to ' . $endLastYear->format('d M') . ')'
        ];

        $this->toView('headings', $headings);
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