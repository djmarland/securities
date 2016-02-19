<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\Finder;
use AppBundle\Controller\Traits\SecurityFilter;
use AppBundle\Presenter\Organism\Sector\SectorPresenter;
use AppBundle\Presenter\Organism\Security\SecurityPresenter;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\ValueObject\ID;
use SecuritiesService\Service\Filter\SecuritiesFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SectorsController extends Controller
{
    use SecurityFilter;
    use Finder;

    public function initialize(Request $request)
    {
        parent::initialize($request);
        $this->toView('currentSection', 'sectors');
    }

    public function listAction()
    {
        $groups = $this->get('app.services.groups')
            ->findAllInSectors();

        $sectorPresenters = [];
        $prevSector = null;
        $collectedGroups = [];
        if (!empty($groups)) {
            foreach ($groups as $group) {
                $sector = $group->getSector();
                if ($sector != $prevSector) {
                    if ($prevSector) {
                        $sectorPresenters[] = new SectorPresenter($prevSector, $collectedGroups);
                    }
                    $prevSector = $sector;
                    $collectedGroups = [];
                }
                $collectedGroups[] = $group;
            }
            if ($prevSector) {
                $sectorPresenters[] = new SectorPresenter($prevSector, $collectedGroups);
            }
        }

        $this->setTitle('Sectors');
        $this->toView('sectors', $sectorPresenters);

        return $this->renderTemplate('sectors:list');
    }

    public function showAction(Request $request)
    {
        $sector = $this->getSector($request);

//        $securitiesService = $this->get('app.services.securities');
//
//        $count = $securitiesService
//            ->countByIssuer($issuer);
//
//        $totalRaised = $securitiesService
//            ->sumByIssuer($issuer);
//
//        $result = $securitiesService
//            ->findLatestForIssuer($issuer, 5);
//
//        $securityPresenters = [];
//        $securities = $result->getDomainModels();
//        if (!empty($securities)) {
//            foreach ($securities as $security) {
//                $securityPresenters[] = new SecurityPresenter($security);
//            }
//        }
//
//        $this->toView('totalRaised', number_format($totalRaised));
//        $this->toView('count', $count);
//        $this->toView('securities', $securityPresenters);
//        $this->toView('hasSecurities', $count > 0);

        return $this->renderTemplate('sectors:show');
    }

    public function securitiesAction(Request $request)
    {
        $sector = $this->getSector($request);

        $filter = new SecuritiesFilter(
            $this->setProductFilter($request),
            $this->setCurrencyFilter($request),
            $this->setBucketFilter($request)
        );

        $perPage = 50;
        $currentPage = $this->getCurrentPage();

        $securitiesService = $this->get('app.services.securities_by_sector');
        $total = $securitiesService->count($sector, $filter);
        $totalRaised = 0;
        $securities = [];
        if ($total) {
            $securities = $securitiesService
                ->find(
                    $sector,
                    $filter,
                    $perPage,
                    $currentPage
                );
            $totalRaised = $securitiesService->sum($sector, $filter);
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

        $this->setPagination(
            $total,
            $currentPage,
            $perPage
        );

        return $this->renderTemplate('sectors:securities');
    }

    private function getSector(Request $request)
    {
        $id = $request->get('sector_id');

        if ($id !== (string) (int) $id) {
            throw new HttpException(404, 'Invalid ID');
        }

        try {
            $sector = $this->get('app.services.sectors')
                ->findByID(new ID((int)$id));
        } catch (EntityNotFoundException $e) {
            throw new HttpException(404, 'Sector ' . $id . ' does not exist.');
        }

        $industry = $sector->getIndustry();

        // I'm looking at a sector, so I need to pass in that sector,
        // and it's parent industry
        $this->setFinder($industry, $sector);

        $this->setTitle($sector->getName());
        $this->toView('sector', $sector);
        return $sector;
    }
}