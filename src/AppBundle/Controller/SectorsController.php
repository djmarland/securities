<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\FinderTrait;
use AppBundle\Controller\Traits\SecurityFilterTrait;
use AppBundle\Presenter\Organism\EntityNav\EntityNavPresenter;
use AppBundle\Presenter\Organism\Issuance\IssuanceGraphPresenter;
use AppBundle\Presenter\Organism\Issuance\IssuanceTablePresenter;
use AppBundle\Presenter\Organism\Sector\SectorPresenter;
use AppBundle\Presenter\Organism\Security\SecurityPresenter;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\Exception\ValidationException;
use SecuritiesService\Domain\ValueObject\UUID;
use SecuritiesService\Service\Filter\SecuritiesFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SectorsController extends Controller
{
    use SecurityFilterTrait;
    use FinderTrait;

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

        $this->toView('entityNav', new EntityNavPresenter($sector, 'show'));
        return $this->renderTemplate('sectors:show');
    }

    public function maturityProfileAction(Request $request)
    {
        throw new HttpException(404, 'Not yet');
//        $this->toView('entityNav', new EntityNavPresenter($group, 'maturity_profile'));
//        return $this->renderTemplate('groups:maturity-profile');
    }

    public function issuanceAction(Request $request)
    {
        $sector = $this->getSector($request);
        $years = $this->get('app.services.securities_by_sector')->issuanceYears($sector);

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
                    'sector_issuance',
                    [
                        'sector_id' => $sector->getId(),
                        'year' => $year,
                    ]
                )
            );
        }

        $this->toView('activeYear', $year);

        $this->toView('years', $years);
        $this->toView('entityNav', new EntityNavPresenter($sector, 'issuance'));

        $results = [];
        if ($year) {
            $results = $this->get('app.services.securities_by_sector')->productCountsByMonthForYear(
                $sector,
                $year
            );
        }

        $hasData = false;
        $issuanceTable = null;
        $issuanceGraph = null;
        if (!empty($results)) {
            $hasData = true;
            $issuanceTable = new IssuanceTablePresenter($sector, $results, $year);
            $issuanceGraph = new IssuanceGraphPresenter($sector, $results, $year);
        }

        $this->toView('hasData', $hasData);
        $this->toView('issuanceTable', $issuanceTable);
        $this->toView('issuanceGraph', $issuanceGraph);

        return $this->renderTemplate('sectors:issuance');
    }

    public function securitiesAction(Request $request)
    {
        $sector = $this->getSector($request);

        $filter = $this->setFilter($request);

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

        $this->toView('entityNav', new EntityNavPresenter($sector, 'securities'));
        return $this->renderTemplate('sectors:securities');
    }

    private function getSector(Request $request)
    {
        $id = $request->get('sector_id');

        try {
            $sector = $this->get('app.services.sectors')
                ->findByUUID(UUID::createFromString($id));
        } catch (ValidationException $e) {
            throw new HttpException(404, $e->getMessage());
        } catch (EntityNotFoundException $e) {
            throw new HttpException(404, $e->getMessage());
        }

        $industry = $sector->getIndustry();

        // I'm looking at a sector, so I need to pass in that sector,
        // and it's parent industry
        $this->setFinder($request->get('_route'), $industry, $sector);

        $this->setTitle($sector->getName());
        $this->toView('sector', $sector);
        return $sector;
    }
}
