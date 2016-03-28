<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\FinderTrait;
use AppBundle\Controller\Traits\IssuanceTrait;
use AppBundle\Controller\Traits\OverviewTrait;
use AppBundle\Controller\Traits\SecuritiesTrait;
use AppBundle\Presenter\Molecule\Money\MoneyPresenter;
use AppBundle\Presenter\Organism\EntityContext\EntityContextPresenter;
use AppBundle\Presenter\Organism\EntityNav\EntityNavPresenter;
use AppBundle\Presenter\Organism\Group\GroupPresenter;
use AppBundle\Presenter\Organism\Security\SecurityPresenter;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\Exception\ValidationException;
use SecuritiesService\Domain\ValueObject\UUID;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GroupsController extends Controller
{
    use SecuritiesTrait;
    use IssuanceTrait;
    use OverviewTrait;
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

        $this->setTitle('Issuers by Parent Group');
        $this->toView('groups', $groupPresenters);

        return $this->renderTemplate('groups:list');
    }

    public function showAction(Request $request)
    {
        $group = $this->getGroup($request);
        return $this->renderOverview($request, $group);
    }

    public function securitiesAction(Request $request)
    {
        $group = $this->getGroup($request);
        return $this->renderSecurities($request, $group);
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
        return $this->renderIssuance($request, $group);
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
        $this->toView('group', $group);
        $this->toView('entityContextPresenter', new EntityContextPresenter($group));

        // I'm looking at a group, so I need to pass in that group,
        // and it's parent sector + industry
        $this->setFinder($request->get('_route'), $industry, $sector, $group);
        return $group;
    }
}
