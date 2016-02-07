<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\SecurityFilter;
use AppBundle\Presenter\Organism\Group\GroupPresenter;
use AppBundle\Presenter\Organism\Issuer\IssuerPresenter;
use AppBundle\Presenter\Organism\Security\SecurityPresenter;
use DateTimeImmutable;
use SecuritiesService\Domain\ValueObject\ID;
use SecuritiesService\Service\SecuritiesFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GroupsController extends Controller
{
    use SecurityFilter;

    public function initialize(Request $request)
    {
        parent::initialize($request);
        $this->toView('currentSection', 'issuers');
    }

    public function listAction()
    {
        $result = $this->get('app.services.issuers')
            ->findAllInGroups();

        $groupPresenters = [];
        $companies = $result->getDomainModels();
        $prevGroup = null;
        $collectedCompanies = [];
        if (!empty($companies)) {
            foreach ($companies as $company) {
                $group = $company->getParentGroup();
                if ($group != $prevGroup) {
                    if ($prevGroup) {
                        $groupPresenters[] = new GroupPresenter($prevGroup, $collectedCompanies);
                    }
                    $prevGroup = $group;
                    $collectedCompanies = [];
                }
                $collectedCompanies[] = $company;
            }
            if ($prevGroup) {
                $groupPresenters[] = new GroupPresenter($prevGroup, $collectedCompanies);
            }
        }

        $this->setTitle('Issuers');
        $this->toView('groups', $groupPresenters);

        return $this->renderTemplate('groups:list');
    }

    public function showAction(Request $request)
    {
        $group = $this->getGroup($request);

        $result = $this->get('app.services.issuers')
            ->findAllByGroup($group);

        $issuers = $result->getDomainModels();
        $this->toView('issuers', $issuers);
        return $this->renderTemplate('groups:show');
    }

    public function issuersAction(Request $request)
    {
        $group = $this->getGroup($request);

        $perPage = 1500;
        $currentPage = $this->getCurrentPage();

        $result = $this->get('app.services.issuers')
            ->findAndCountAllByGroup($group, $perPage, $currentPage);

        $issuerPresenters = [];
        $issuers = $result->getDomainModels();
        if (!empty($issuers)) {
            foreach ($issuers as $issuer) {
                $issuerPresenters[] = new IssuerPresenter($issuer);
            }
        }

        $this->toView('issuers', $issuerPresenters);
        $this->toView('total', $result->getTotal());

        $this->setPagination(
            $result->getTotal(),
            $currentPage,
            $perPage
        );

        return $this->renderTemplate('groups:issuers');
    }

    public function securitiesAction(Request $request)
    {
        $group = $this->getGroup($request);

        $product = $this->setProductFilter($request);
        $currency = $this->setCurrencyFilter($request);
        $bucket = $this->setBucketFilter($request);

        $filter = new SecuritiesFilter(
            $product,
            $currency,
            $bucket
        );

        $perPage = 50;
        $currentPage = $this->getCurrentPage();

        $securitiesService = $this->get('app.services.securities');
        $result = $securitiesService
            ->findAndCountByGroup(
                $group,
                $perPage,
                $currentPage,
                $filter
            );

        $totalRaised = $securitiesService
            ->sumByGroup(
                $group,
                $filter
            );

        $securityPresenters = [];
        $securities = $result->getDomainModels();
        if (!empty($securities)) {
            foreach ($securities as $security) {
                $securityPresenters[] = new SecurityPresenter($security);
            }
        }

        $this->toView('totalRaised', number_format($totalRaised));
        $this->toView('securities', $securityPresenters);
        $this->toView('total', $result->getTotal());

        $this->setPagination(
            $result->getTotal(),
            $currentPage,
            $perPage
        );

        return $this->renderTemplate('groups:securities');
    }

    public function yieldAction(Request $request)
    {
        $group = $this->getGroup($request);
        $today = new DateTimeImmutable(); // @todo - use global app time

        $graphData = [];
        $years = [];
        $hasData = false;

        $largestValue = 0;

        // last three years
        for ($i = 0; $i < 3; $i++) {
            $year = $today->sub(new \DateInterval('P' . $i . 'Y'));
            $year = (int) $year->format('Y');
            $years[] = $year;


            $result = $this->get('app.services.yields')->findByParentGroupForYear($group, $year);
            if (!$result->hasResult()) {
                continue;
            }
            $hasData = true;
            $yieldData = $result->getDomainModel();

            foreach($yieldData->getDataPoints() as $pointYear => $point) {
                $pointYear = (int) $pointYear;
                if (!isset($graphData[$pointYear])) {
                    $graphData[$pointYear] = [
                        $pointYear,
                        null,
                        null,
                        null
                    ];
                }
                $graphData[$pointYear][$i+1] = $point;
                if ($point > $largestValue) {
                    $largestValue = $point;
                }
            }
        }

        $graphData = array_values($graphData);
//        foreach($years as $y) {
//            $graphData[] = array_values($y);
//        }
//        var_dump($graphData);die;

//        if ($yieldData) {
//            foreach ($yieldData->getDataPoints() as $pointYear => $point) {
//                $graphData[] = [(int)$pointYear, $point];
//            }
//        }

        $this->toView('graphData', $graphData);
        $this->toView('hasData', $hasData);
        $this->toView('largestPoint', $largestValue);
        $this->toView('years', $years);
        return $this->renderTemplate('groups:yield');
    }

    private function getGroup(Request $request)
    {
        $id = $request->get('group_id');

        if ($id !== (string) (int) $id) {
            throw new HttpException(404, 'Invalid ID');
        }

        $result = $this->get('app.services.groups')
            ->findByID(new ID((int) $id));

        if (!$result->hasResult()) {
            throw new HttpException(404, 'Group ' . $id . ' does not exist.');
        }
        $group = $result->getDomainModel();

        $this->setTitle($group->getName());
        $this->toView('group', $group);
        return $group;
    }
}