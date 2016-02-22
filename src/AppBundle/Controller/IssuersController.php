<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\FinderTrait;
use AppBundle\Controller\Traits\SecurityFilterTrait;
use AppBundle\Presenter\Organism\EntityNav\EntityNavPresenter;
use AppBundle\Presenter\Organism\Issuance\IssuanceTablePresenter;
use AppBundle\Presenter\Organism\Issuance\IssuanceGraphPresenter;
use AppBundle\Presenter\Organism\Issuer\IssuerPresenter;
use AppBundle\Presenter\Organism\Security\SecurityPresenter;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\Exception\ValidationException;
use SecuritiesService\Domain\ValueObject\UUID;
use SecuritiesService\Service\Filter\SecuritiesFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class IssuersController extends Controller
{
    use SecurityFilterTrait;
    use FinderTrait;

    public function initialize(Request $request)
    {
        parent::initialize($request);
        $this->toView('currentSection', 'issuers');
    }

    public function listAction()
    {
        $perPage = 1500;
        $currentPage = $this->getCurrentPage();

        $total = $this->get('app.services.issuers')
            ->countAll();
        $issuers = [];
        if ($total) {
            $issuers = $this->get('app.services.issuers')
                ->findAll($perPage, $currentPage);
        }

        $issuerPresenters = [];
        if (!empty($issuers)) {
            foreach ($issuers as $issuer) {
                $issuerPresenters[] = new IssuerPresenter($issuer);
            }
        }

        $letterGroups = [];
        foreach ($issuerPresenters as $issuer) {
            if (!isset($letterGroups[$issuer->getLetter()])) {
                $letterGroups[$issuer->getLetter()] = [];
            }
            $letterGroups[$issuer->getLetter()][] = $issuer;
        }

        $allLetters = array_merge(['#'], range('A', 'Z'));
        $letters = [];
        foreach ($allLetters as $letter) {
            $letters[] = (object) [
                'text' => $letter,
                'active' => isset($letterGroups[$letter]),
            ];
        }

        $this->setTitle('Issuers');
        $this->toView('letters', $letters);
        $this->toView('groups', $letterGroups);
        $this->toView('total', $total);

        $this->setPagination(
            $total,
            $currentPage,
            $perPage
        );

        return $this->renderTemplate('issuers:list');
    }

    public function showAction(Request $request)
    {
        $issuer = $this->getIssuer($request);

        $securitiesService = $this->get('app.services.securities_by_issuer');

        $count = $securitiesService
            ->count($issuer);

        $totalRaised = $securitiesService
            ->sum($issuer);

        $securities = $securitiesService
            ->findLatest($issuer, 5);

        $securityPresenters = [];
        if (!empty($securities)) {
            foreach ($securities as $security) {
                $securityPresenters[] = new SecurityPresenter($security);
            }
        }

        $this->toView('totalRaised', number_format($totalRaised));
        $this->toView('count', $count);
        $this->toView('securities', $securityPresenters);
        $this->toView('hasSecurities', $count > 0);
        $this->toView('entityNav', new EntityNavPresenter($issuer, 'show'));

        return $this->renderTemplate('issuers:show');
    }

    public function securitiesAction(Request $request)
    {
        $issuer = $this->getIssuer($request);

        $filter = $this->setFilter($request);

        $perPage = 50;
        $currentPage = $this->getCurrentPage();

        $securitiesService = $this->get('app.services.securities_by_issuer');
        $total = $securitiesService->count($issuer, $filter);
        $totalRaised = 0;
        $securities = [];
        if ($total) {
            $securities = $securitiesService
                ->find(
                    $issuer,
                    $filter,
                    $perPage,
                    $currentPage
                );
            $totalRaised = $securitiesService->sum($issuer, $filter);
        }

        $securityPresenters = [];
        if (!empty($securities)) {
            foreach ($securities as $security) {
                $securityPresenters[] = new SecurityPresenter($security);
            }
        }

        $this->toView('totalRaised', number_format($totalRaised));
        $this->toView('securities', $securityPresenters);
        $this->toView('total', $total);
        $this->toView('entityNav', new EntityNavPresenter($issuer, 'securities'));

        $this->setPagination(
            $total,
            $currentPage,
            $perPage
        );

        return $this->renderTemplate('issuers:securities');
    }

    public function maturityProfileAction(Request $request)
    {
        $issuer = $this->getIssuer($request);

        $products = $this->get('app.services.products')->findAll();
        $buckets = $this->get('app.services.buckets')->getAll(new \DateTime()); // @todo - use global app time

        $tableData = [];
        $bucketTotals = [];
        $absoluteTotal = 0;

        $filter = new SecuritiesFilter(
            $this->setProductFilter($request),
            $this->setBucketFilter($request)
        );

        foreach ($products as $product) {
            $rowData = (object) [
                'product' => $product,
                'columns' => [],
                'total' => 0,
            ];
            foreach ($buckets as $key => $bucket) {
                $amount = $this->get('app.services.securities_by_issuer')->sum(
                    $issuer,
                    $filter
                );
                $rowData->total += $amount;
                $empty = (object) [
                    'bucket' => $bucket,
                    'amount' => 0,
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
        $this->toView('entityNav', new EntityNavPresenter($issuer, 'maturity_profile'));
        return $this->renderTemplate('issuers:maturity-profile');
    }

    public function issuanceAction(Request $request)
    {
        $issuer = $this->getIssuer($request);
        $years = $this->get('app.services.securities_by_issuer')->issuanceYears($issuer);

        // only show years after 3 years ago (@todo - abstract)
        $currentYear = (int) $this->getApplicationTime()->format('Y');
        $years = array_filter($years, function($year) use ($currentYear) {
            return $year >= $currentYear-3;
        });
        $year = $this->getYear($request, $this->getApplicationTime());
        if (is_null($year)) {
            if (!empty($years)) {
                $year = reset($years);
            } else {
                $year = $currentYear;
            }
            return $this->redirect(
                $this->generateUrl(
                    'issuer_issuance',
                    [
                        'issuer_id' => $issuer->getId(),
                        'year' => $year,
                    ]
                )
            );
        }

        $this->toView('activeYear', $year);
        $this->toView('years', $years);
        $this->toView('entityNav', new EntityNavPresenter($issuer, 'issuance'));

        $results = [];
        if ($year) {
            $results = $this->get('app.services.securities_by_issuer')->productCountsByMonthForYear(
                $issuer,
                $year
            );
        }

        $hasData = false;
        $issuanceTable = null;
        $issuanceGraph = null;
        if (!empty($results)) {
            $hasData = true;
            $issuanceTable = new IssuanceTablePresenter($issuer, $results, $year);
            $issuanceGraph = new IssuanceGraphPresenter($issuer, $results, $year);
        }

        $this->toView('hasData', $hasData);
        $this->toView('issuanceTable', $issuanceTable);
        $this->toView('issuanceGraph', $issuanceGraph);

        return $this->renderTemplate('issuers:issuance');
    }

    private function getIssuer(Request $request)
    {
        $id = $request->get('issuer_id');

        try {
            $issuer = $this->get('app.services.issuers')
                ->findByUUID(UUID::createFromString($id));
        } catch (ValidationException $e) {
            throw new HttpException(404, $e->getMessage());
        } catch (EntityNotFoundException $e) {
            throw new HttpException(404, $e->getMessage());
        }

        $sector = null;
        $industry = null;

        $group = $issuer->getParentGroup();
        if ($group) {
            $sector = $group->getSector();
        }
        if ($sector) {
            $industry = $sector->getIndustry();
        }

        // I'm looking at a group, so I need to pass in that issuer,
        // and it's parent group, sector + industry
        $this->setFinder($request->get('_route'), $industry, $sector, $group, $issuer);

        $this->setTitle($issuer->getName());
        $this->toView('issuer', $issuer);
        return $issuer;
    }
}
