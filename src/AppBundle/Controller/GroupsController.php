<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\FinderTrait;
use AppBundle\Controller\Traits\SecurityFilterTrait;
use AppBundle\Presenter\Molecule\Money\MoneyPresenter;
use AppBundle\Presenter\Organism\EntityNav\EntityNavPresenter;
use AppBundle\Presenter\Organism\Group\GroupPresenter;
use AppBundle\Presenter\Organism\Issuance\IssuanceGraphPresenter;
use AppBundle\Presenter\Organism\Issuance\IssuanceTablePresenter;
use AppBundle\Presenter\Organism\Security\SecurityPresenter;
use DateTimeImmutable;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\Exception\ValidationException;
use SecuritiesService\Domain\ValueObject\UUID;
use SecuritiesService\Service\Filter\SecuritiesFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GroupsController extends Controller
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
        $companies = $this->get('app.services.issuers')
            ->findAllInGroups();

        $groupPresenters = [];
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

        $securitiesService = $this->get('app.services.securities_by_group');

        $count = $securitiesService
            ->count($group);

        $totalRaised = $securitiesService
            ->sum($group);

        $securities = $securitiesService
            ->findNextMaturing($group, 2);

        $securityPresenters = [];
        if (!empty($securities)) {
            foreach ($securities as $security) {
                $securityPresenters[] = new SecurityPresenter($security, [
                    'template' => 'simple',
                ]);
            }
        }

        $this->setTitle($group->getName());
        $this->toView('totalRaised', new MoneyPresenter($totalRaised, ['scale' => true]));
        $this->toView('count', number_format($count));
        $this->toView('securities', $securityPresenters);
        $this->toView('hasSecurities', $count > 0);
        $this->toView('entityNav', new EntityNavPresenter($group, 'show'));

        return $this->renderTemplate('groups:show');
    }

    public function securitiesAction(Request $request)
    {
        $group = $this->getGroup($request);

        $filter = $this->setFilter($request);

        $perPage = 50;
        $currentPage = $this->getCurrentPage();

        $securitiesService = $this->get('app.services.securities_by_group');
        $total = $securitiesService->count($group, $filter);
        $totalRaised = 0;
        $securities = [];
        if ($total) {
            $securities = $securitiesService
                ->find(
                    $group,
                    $filter,
                    $perPage,
                    $currentPage
                );
            $totalRaised = $securitiesService->sum($group, $filter);
        }

        $securityPresenters = [];
        if (!empty($securities)) {
            foreach ($securities as $security) {
                $securityPresenters[] = new SecurityPresenter($security);
            }
        }

        $this->setTitle('Securities ' . $group->getName());
        $this->toView('totalRaised', new MoneyPresenter($totalRaised, ['scale' => true]));
        $this->toView('securities', $securityPresenters);
        $this->toView('total', $total);

        $this->setPagination(
            $total,
            $currentPage,
            $perPage
        );

        $this->toView('entityNav', new EntityNavPresenter($group, 'securities'));
        return $this->renderTemplate('groups:securities');
    }

    public function maturityProfileAction(Request $request)
    {
        throw new HttpException(404, 'Not yet');
//        $this->setTitle('Issuance ' . $year . ' - ' . $group->getName());
//        $this->toView('entityNav', new EntityNavPresenter($group, 'maturity_profile'));
//        return $this->renderTemplate('groups:maturity-profile');
    }

    public function issuanceAction(Request $request)
    {
        $group = $this->getGroup($request);
        $years = $this->get('app.services.securities_by_group')->issuanceYears($group);

        // only show years after 3 years ago (@todo - abstract)
        $currentYear = (int) $this->getApplicationTime()->format('Y');
        $years = array_filter($years, function ($year) use ($currentYear) {
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
                    'group_issuance',
                    [
                        'group_id' => $group->getId(),
                        'year' => $year,
                    ]
                )
            );
        }

        $this->toView('activeYear', $year);
        $this->toView('years', $years);
        $this->toView('entityNav', new EntityNavPresenter($group, 'issuance'));

        $results = [];
        if ($year) {
            $results = $this->get('app.services.securities_by_group')->productCountsByMonthForYear(
                $group,
                $year
            );
        }

        $hasData = false;
        $issuanceTable = null;
        $issuanceGraph = null;
        if (!empty($results)) {
            $hasData = true;
            $issuanceTable = new IssuanceTablePresenter($group, $results, $year);
            $issuanceGraph = new IssuanceGraphPresenter($group, $results, $year);
        }

        $this->setTitle('Issuance ' . $year . ' - ' . $group->getName());
        $this->toView('hasData', $hasData);
        $this->toView('issuanceTable', $issuanceTable);
        $this->toView('issuanceGraph', $issuanceGraph);

        return $this->renderTemplate('groups:issuance');
    }

    public function yieldAction(Request $request)
    {
        throw new HttpException(404, 'Not yet');
//        $group = $this->getGroup($request);
//        $today = new DateTimeImmutable(); // @todo - use global app time
//
//        $graphData = [];
//        $years = [];
//        $hasData = false;
//
//        $largestValue = 0;
//
//        // last three years
//        for ($i = 0; $i < 3; $i++) {
//            $year = $today->sub(new \DateInterval('P' . $i . 'Y'));
//            $year = (int) $year->format('Y');
//            $years[] = $year;
//
//
//            $result = $this->get('app.services.yields')->findByParentGroupForYear($group, $year);
//            if (!$result->hasResult()) {
//                continue;
//            }
//            $hasData = true;
//            $yieldData = $result->getDomainModel();
//
//            foreach ($yieldData->getDataPoints() as $pointYear => $point) {
//                $pointYear = (int) $pointYear;
//                if (!isset($graphData[$pointYear])) {
//                    $graphData[$pointYear] = [
//                        $pointYear,
//                        null,
//                        null,
//                        null,
//                    ];
//                }
//                $graphData[$pointYear][$i+1] = $point;
//                if ($point > $largestValue) {
//                    $largestValue = $point;
//                }
//            }
//        }
//
//        $graphData = array_values($graphData);
//        foreach($years as $y) {
//            $graphData[] = array_values($y);
//        }
//        var_dump($graphData);die;

//        if ($yieldData) {
//            foreach ($yieldData->getDataPoints() as $pointYear => $point) {
//                $graphData[] = [(int)$pointYear, $point];
//            }
//        }
//
//        $this->toView('graphData', $graphData);
//        $this->toView('hasData', $hasData);
//        $this->toView('largestPoint', $largestValue);
//        $this->toView('years', $years);
//        return $this->renderTemplate('groups:yield');
    }

    private function getGroup(Request $request)
    {
        $id = $request->get('group_id');

        try {
            $group = $this->get('app.services.groups')
                ->findByUUID(UUID::createFromString($id));
        } catch (ValidationException $e) {
            throw new HttpException(404, $e->getMessage());
        } catch (EntityNotFoundException $e) {
            throw new HttpException(404, $e->getMessage());
        }

        $industry = null;
        $sector = $group->getSector();

        if ($sector) {
            $industry = $sector->getIndustry();
        }

        // I'm looking at a group, so I need to pass in that group,
        // and it's parent sector + industry
        $this->setFinder($request->get('_route'), $industry, $sector, $group);

        $this->toView('group', $group);
        return $group;
    }
}
