<?php

namespace AppBundle\Controller;

use DateTimeImmutable;
use SecuritiesService\Domain\Entity\Enum\Features;
use SecuritiesService\Domain\Entity\Security;
use SecuritiesService\Service\Filter\SecuritiesFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ReportsController extends Controller
{

    public function listAction()
    {
        throw new HttpException(404, 'Not yet');
//        return $this->renderTemplate('reports:list');
    }

    public function weeklyAction(Request $request)
    {
        if (!$this->appConfig->featureIsActive(Features::WEEKLY_REPORT_ACTIVE())) {
            throw new HttpException(404, 'Not yet');
        }

        $year = $this->request->get('year');
        $month = $this->request->get('month');
        $day = $this->request->get('day');

        $date = new DateTimeImmutable($year . '-' . $month . '-' . $day . 'T00:00Z');

        // weekly reports come out on a Monday. Any other day of the week should
        // redirect to Monday
        $dayOfWeek = $date->format('N') - 1;
        if ($dayOfWeek) {
            $monday = $date->sub(new \DateInterval('P' . $dayOfWeek . 'D'));
            return $this->redirectToRoute('report_weekly', [
                'day' => $monday->format('d'),
                'month' => $monday->format('m'),
                'year' => $monday->format('Y'),
            ]);
        }

        $twoWeeks = new \DateInterval('P14D');
        $oneWeek = new \DateInterval('P7D');

        $oneWeekAgo = $date->sub($oneWeek);
        $twoWeeksAgo = $date->sub($twoWeeks);

        $filter = new SecuritiesFilter([
            'start' => $twoWeeksAgo,
            'end' => $date
        ]);
        $securities = $this->get('app.services.securities')
            ->find(1000, 1, $filter);

        $lastWeek = [];
        $thisWeek = [];

        // start to build the charts
        foreach ($securities as $security) {
            /** @var Security $security */
            if ($security->getStartDate() > $oneWeekAgo) {
                $thisWeek[] = $security;
            } else {
                $lastWeek[] = $security;
            }
        }



        // to build a bubble chart
        $colours = [
            "#634D7B", "#B66D6D", "#B6B16D", "#579157", '#777', "#342638"
        ];
        $headings = [];
        $chartData = [];
        $largest = 0;
        foreach ($securities as $security) {
            if ($security->getCoupon() && $security->getMaturityDate()) {
                $amount = $security->getMoneyRaised();
                if ($amount > $largest) {
                    $largest = $amount;
                }
            }
        }


        foreach ($securities as $security) {
            if ($security->getCoupon() && $security->getMaturityDate()) {
                $headings[] = $security->getIsin() . ': ' . $security->getCompany()->getName();
                $chartData[] = [
                    'x' => $security->getTerm(),
                    'y' => $security->getCoupon() * 100,
                    'r' => ($security->getMoneyRaised() / $largest) * 100,
                    'label' => $security->getIsin() . ': ' . $security->getCompany()->getName()
                ];
            }
        }

        $this->toView('chartColours', $colours);
        $this->toView('chartHeadings', $headings);
        $this->toView('weekDate', $date->format('D j F Y'));
        $this->toView('chartData', $chartData);
        return $this->renderTemplate('reports:weekly');
    }

    public function fsa50Action(Request $request)
    {
        throw new HttpException(404, 'Not yet');
//        $products = $this->get('app.services.products')
//            ->findAll()->getDomainModels();
//
//        $product = $this->setProductFilter($request);
//
//        $endThisYear = new \DateTimeImmutable('2015-11-20'); // @todo - based on today's date?
//        $endLastYear = $endThisYear->sub(new \DateInterval('P1Y'));
//
//        $resultsThis = $this->get('app.services.securities')->sumForProductGroupedByCountryForYearToDate(
//            $endThisYear,
//            $product
//        );
//        $resultsLast = $this->get('app.services.securities')->sumForProductGroupedByCountryForYearToDate(
//            $endLastYear,
//            $product
//        );
//
//        $countries = array_unique(array_merge(array_keys($resultsThis), array_keys($resultsLast)));
//        $rows = [];
//
//        foreach ($countries as $country) {
//            $rows[] = [
//                $country,
//                $resultsThis[$country] ?? 0,
//                $resultsLast[$country] ?? 0,
//            ];
//        }
//
//        usort($rows, function ($a, $b) {
//            return $b[1] <=> $a[1];
//        });
//
//        $other = array_slice($rows, 9);
//        $rows = array_slice($rows, 0, 9);
//
//        if (!empty($other)) {
//            $otherRow = [
//                'Other',
//                0,
//                0,
//            ];
//
//            foreach ($other as $o) {
//                $otherRow[1] += $o[1];
//                $otherRow[2] += $o[2];
//            }
//
//            $rows[] = $otherRow;
//        }
//
//        $headings = [
//            'Country',
//            $endThisYear->format('Y') . ' (to ' . $endThisYear->format('d M') . ')',
//            $endLastYear->format('Y') . ' (to ' . $endLastYear->format('d M') . ')',
//        ];
//
//        $this->toView('headings', $headings);
//        $this->toView('products', $products);
//        $this->toView('rows', $rows);
//        $this->toView('graphData', array_merge(
//            [$headings],
//            $rows
//        ));
//        return $this->renderTemplate('reports:fsa50');
    }

    public function fsa54Action(Request $request)
    {
        throw new HttpException(404, 'Not yet');
//        $products = $this->get('app.services.products')
//            ->findAll()->getDomainModels();
//
//        $product = $this->setProductFilter($request);
//
//        $endThisYear = new \DateTimeImmutable('2015-11-20'); // @todo - based on today's date?
//        $endLastYear = $endThisYear->sub(new \DateInterval('P1Y'));
//
//        $resultsThis = $this->get('app.services.securities')->sumForProductGroupedByCurrencyForYearToDate(
//            $endThisYear,
//            $product
//        );
//        $resultsLast = $this->get('app.services.securities')->sumForProductGroupedByCurrencyForYearToDate(
//            $endLastYear,
//            $product
//        );
//
//        $currencies = array_unique(array_merge(array_keys($resultsThis), array_keys($resultsLast)));
//        $rows = [];
//
//        foreach ($currencies as $currency) {
//            $rows[] = [
//                $currency,
//                $resultsThis[$currency] ?? 0,
//                $resultsLast[$currency] ?? 0,
//            ];
//        }
//
//        usort($rows, function ($a, $b) {
//            return $b[1] <=> $a[1];
//        });
//
//        $other = array_slice($rows, 3);
//        $rows = array_slice($rows, 0, 3);
//
//        if (!empty($other)) {
//            $otherRow = [
//                'Other',
//                0,
//                0,
//            ];
//
//            foreach ($other as $o) {
//                $otherRow[1] += $o[1];
//                $otherRow[2] += $o[2];
//            }
//
//            $rows[] = $otherRow;
//        }
//
//        $headings = [
//            'Currency',
//            $endThisYear->format('Y') . ' (to ' . $endThisYear->format('d M') . ')',
//            $endLastYear->format('Y') . ' (to ' . $endLastYear->format('d M') . ')',
//        ];
//
//        $this->toView('headings', $headings);
//        $this->toView('products', $products);
//        $this->toView('rows', $rows);
//        $this->toView('graphData', array_merge(
//            [$headings],
//            $rows
//        ));
//        return $this->renderTemplate('reports:fsa54');
    }
}
