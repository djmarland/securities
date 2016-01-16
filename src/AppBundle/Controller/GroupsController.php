<?php

namespace AppBundle\Controller;

use AppBundle\Presenter\Organism\Group\GroupPresenter;
use AppBundle\Presenter\Organism\Issuer\IssuerPresenter;
use DateTimeImmutable;
use SecuritiesService\Domain\Entity\ParentGroup;
use SecuritiesService\Domain\ValueObject\ID;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GroupsController extends Controller
{
    public function initialize(Request $request)
    {
        parent::initialize($request);
        $this->toView('currentSection', 'groups');
    }

    public function listAction()
    {
        $perPage = 1500;
        $currentPage = $this->getCurrentPage();

        $result = $this->get('app.services.groups')
            ->findAndCountAll($perPage, $currentPage);

        $groupPresenters = [];
        $groups = $result->getDomainModels();
        if (!empty($groups)) {
            foreach ($groups as $group) {
                $groupPresenters[] = new GroupPresenter($group);
            }
        }

        $this->setTitle('Groups');
        $this->toView('groups', $groupPresenters);
        $this->toView('total', $result->getTotal());

        $this->setPagination(
            $result->getTotal(),
            $currentPage,
            $perPage
        );

        return $this->renderTemplate('groups:list');
    }

    public function showAction(Request $request)
    {
        $group = $this->getGroup($request);

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

        return $this->renderTemplate('groups:securities');
    }

    public function yieldAction(Request $request)
    {
        $group = $this->getGroup($request);

        $today = new DateTimeImmutable(); // @todo - use global app time
        $year = $this->getYear($request, $today);
        // @todo - move this logic into the shared method
        if (is_null($year)) {
            if ($today->format('m') == 1) {
                // redirect january to last year, as we won't have any data yet
                return $this->redirect(
                    $this->generateUrl(
                        'groups_yield',
                        [
                            'group_id' => $group->getId(),
                            'year' => $today->format('Y')-1
                        ]
                    )
                );
            }
        }

        $result = $this->get('app.services.yields')->findByParentGroupForYear($group, $year);
        $yieldData = $result->getDomainModel();

        $graphData = [];
        foreach ($yieldData->getDataPoints() as $pointYear => $point) {
            $graphData[] = [(int) $pointYear, $point];
        }

        $this->toView('graphData', $graphData);
        $this->toView('yieldData', $yieldData);
        $this->toView('activeYear', $year);
        $this->toView('years', $this->getYearsForYieldCurves($group)); // @todo
        return $this->renderTemplate('groups:yield');
    }

    private function getYearsForYieldCurves(ParentGroup $issuer): array
    {
        // @todo - calculate valid years for this issuer
        return [
            2016, 2015, 2014
        ];
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