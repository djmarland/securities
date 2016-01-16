<?php

namespace AppBundle\Controller;

use AppBundle\Presenter\Organism\Issuer\IssuerPresenter;
use AppBundle\Presenter\Organism\Security\SecurityPresenter;
use SecuritiesService\Domain\Entity\Company;
use SecuritiesService\Domain\ValueObject\Bucket;
use SecuritiesService\Domain\ValueObject\ID;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use DateTimeImmutable;

class IssuersController extends Controller
{
    public function initialize(Request $request)
    {
        parent::initialize($request);
        $this->toView('currentSection', 'issuers');
    }

    public function listAction()
    {
        $perPage = 1500;
        $currentPage = $this->getCurrentPage();

        $result = $this->get('app.services.issuers')
            ->findAndCountAll($perPage, $currentPage);

        $issuerPresenters = [];
        $issuers = $result->getDomainModels();
        if (!empty($issuers)) {
            foreach ($issuers as $issuer) {
                $issuerPresenters[] = new IssuerPresenter($issuer);
            }
        }

        $this->setTitle('Issuers');
        $this->toView('issuers', $issuerPresenters);
        $this->toView('total', $result->getTotal());

        $this->setPagination(
            $result->getTotal(),
            $currentPage,
            $perPage
        );

        return $this->renderTemplate('issuers:list');
    }

    public function showAction(Request $request)
    {
        $issuer = $this->getIssuer($request);

        $perPage = 50;
        $currentPage = $this->getCurrentPage();

        $securitiesService = $this->get('app.services.securities');

        $result = $securitiesService
            ->findAndCountByIssuer($issuer, $perPage, $currentPage);

        $totalRaised = $securitiesService
            ->sumByIssuer($issuer);

        $securityPresenters = [];
        $securities = $result->getDomainModels();
        if (!empty($securities)) {
            foreach ($securities as $security) {
                $securityPresenters[] = new SecurityPresenter($security);
            }
        }

        $this->toView('activeProduct', 'all');
        $this->toView('products', $this->getProductsForIssuer($issuer));
        $this->toView('totalRaised', number_format($totalRaised));
        $this->toView('securities', $securityPresenters);
        $this->toView('total', $result->getTotal());

        $this->setPagination(
            $result->getTotal(),
            $currentPage,
            $perPage
        );

        return $this->renderTemplate('issuers:show');
    }

    public function securitiesAction(Request $request)
    {
        $issuer = $this->getIssuer($request);

        $product = $this->getProduct($request);
        $bucket = $this->getBucket($request);

        $perPage = 50;
        $currentPage = $this->getCurrentPage();

        $securitiesService = $this->get('app.services.securities');
        $result = $securitiesService
            ->findAndCountByIssuerAndProduct($issuer, $product, $perPage, $currentPage);

        $totalRaised = $securitiesService
            ->sumByIssuerAndProduct($issuer, $product);

        $securityPresenters = [];
        $securities = $result->getDomainModels();
        if (!empty($securities)) {
            foreach ($securities as $security) {
                $securityPresenters[] = new SecurityPresenter($security);
            }
        }

        $this->toView('activeProduct', $product ? $product->getId() : 'all');
        $this->toView('products', $this->getProductsForIssuer($issuer));
        $this->toView('totalRaised', number_format($totalRaised));
        $this->toView('securities', $securityPresenters);
        $this->toView('total', $result->getTotal());

        $this->setPagination(
            $result->getTotal(),
            $currentPage,
            $perPage
        );

        return $this->renderTemplate('issuers:show');
    }

    public function maturityProfileAction(Request $request)
    {
        $issuer = $this->getIssuer($request);

        $products = $this->getProductsForIssuer($issuer);
        $buckets = $this->get('app.services.buckets')->getAll(new \DateTime()); // @todo - use global app time
        $buckets = $buckets->getDomainModels();

        $tableData = [];
        $bucketTotals = [];
        $absoluteTotal = 0;
        foreach($products as $product) {
            $rowData = (object) [
                'product' => $product,
                'columns' => [],
                'total' => 0
            ];
            foreach($buckets as $key => $bucket) {
                $amount = $this->get('app.services.securities')->sumByIssuerProductAndBucket(
                    $issuer,
                    $product,
                    $bucket
                );
                $rowData->total += $amount;
                $empty = (object) [
                    'bucket'=> $bucket,
                    'amount' => 0
                ];
                if (!isset($rowData->columns[$key])) {
                    $rowData->columns[$key] = $empty;
                }
                if (!isset($bucketTotals[$key])) {
                    $bucketTotals[$key] = $empty;
                }
                $rowData->columns[$key]->amount = $amount;
                $bucketTotals[$key]->amount += $amount;
                $absoluteTotal += $amount;
            }
            $tableData[] = $rowData;
        }

        // @todo - create a twig helper for displaying numbers
        $this->toView('buckets', $buckets);
        $this->toView('tableData', $tableData);
        $this->toView('absoluteTotal', $absoluteTotal);
        $this->toView('bucketTotals', $bucketTotals);
        return $this->renderTemplate('issuers:maturity-profile');
    }

    public function issuanceAction(Request $request)
    {
        $issuer = $this->getIssuer($request);

        $today = new DateTimeImmutable(); // @todo - use global app time
        $year = $this->getYear($request, $today);
        if (is_null($year)) {
            if ($today->format('m') == 1) {
                // redirect january to last year, as we won't have any data yet
                return $this->redirect(
                    $this->generateUrl(
                        'issuers_issuance',
                        [
                            'issuer_id' => $issuer->getId(),
                            'year' => $today->format('Y')-1
                        ]
                    )
                );
            }
        }


        $results = $this->get('app.services.securities')->countProductsByIssuerForYear(
            $issuer,
            $year
        );
        $products = [];
        // extract the products from the results
        foreach ($results as $month) {
            foreach($month as $monthValue) {
                $products[$monthValue->product->getId()->getValue()] = $monthValue->product;
            }
        }

        $productCounts = [];
        $graphData = [
            array_map(function($product) {
                return $product->getName();
            }, $products)
        ];
        array_unshift($graphData[0], 'Month');
        $graphData[0][] = (object) [
            'role' => 'annotation'
        ];
        $monthCounts = [];

        $months = [
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
        // for each month, count how many of each product type were issued
//        $securitiesService = $this->get('app.services.securities');
        foreach($products as $product) {
            $productYear = (object) [
                'product' => $product,
                'months' => []
            ];
            foreach ($months as $month => $name) {
//                $count = $securitiesService->countByIssuerProductForMonth(
//                    $issuer,
//                    $product,
//                    $year,
//                    $month
//                );
                $count = $results[$month][$product->getId()->getValue()]->total ?? 0;
                $monthCounts[$month][] = $count;
                $productYear->months[$month] = $count ? $count : '-';
            }
            $productCounts[] = $productYear;
        }

        foreach($months as $num => $month) {
            $row = [
                $month
            ];
            $graphData[] = array_merge($row, $monthCounts[$num], ['']);
        }

        $this->toView('months', $months);
        $this->toView('products', $products);
        $this->toView('graphData', $graphData);
        $this->toView('productCounts', $productCounts);
        $this->toView('years', $this->getYearsForIssuer($issuer)); // @todo
        $this->toView('activeYear', $year);
        return $this->renderTemplate('issuers:issuance');
    }

    private function getProduct(Request $request)
    {
        $productID = $request->get('product');
        if (is_null($productID)) {
            return null;
        }

        $productParamInt = (int) $productID;
        if ($productID !== (string) $productParamInt ||
            $productParamInt <= 0) {
            throw new HttpException(404, 'Invalid ID');
        }

        $result = $this->get('app.services.products')
            ->findById(new ID((int) $productParamInt));
        if (!$result->hasResult()) {
            throw new HttpException(404, 'Product ' . $productID . ' does not exist.');
        }
        return $result->getDomainModel();
    }

    private function getBucket(Request $request)
    {
        $bucketID = $request->get('bucket');
        if (is_null($bucketID)) {
            return null;
        }

        $bucketParamInt = (int) $bucketID;
        if ($bucketID !== (string) $bucketParamInt ||
            $bucketParamInt <= 0) {
            throw new HttpException(404, 'Invalid ID');
        }

        $result = $this->get('app.services.buckets')
            ->findById(new ID((int) $bucketParamInt));
        if (!$result->hasResult()) {
            throw new HttpException(404, 'Product ' . $bucketID . ' does not exist.');
        }
        return $result->getDomainModel();
    }

    private function getIssuer(Request $request)
    {
        $id = $request->get('issuer_id');

        if ($id !== (string) (int) $id) {
            throw new HttpException(404, 'Invalid ID');
        }

        $result = $this->get('app.services.issuers')
            ->findByID(new ID((int) $id));

        if (!$result->hasResult()) {
            throw new HttpException(404, 'Issuer ' . $id . ' does not exist.');
        }
        $issuer = $result->getDomainModel();

        $this->setTitle($issuer->getName());
        $this->toView('issuer', $issuer);
        return $issuer;
    }

    private function getYearsForIssuer(Company $issuer): array
    {
        // @todo - calculate valid years for this issuer
        return [
            2016, 2015, 2014, 2013, 2012
        ];
    }

    private function getProductsForIssuer(Company $issuer): array
    {
        $result = $this->get('app.services.products')->findAllByIssuer($issuer);
        return $result->getDomainModels();
    }
}
