<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\FinderTrait;
use AppBundle\Controller\Traits\IssuanceTrait;
use AppBundle\Controller\Traits\SecurityFilterTrait;
use AppBundle\Presenter\Molecule\Money\MoneyPresenter;
use AppBundle\Presenter\Organism\EntityContext\EntityContextPresenter;
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
    use IssuanceTrait;
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

        $securitiesService = $this->get('app.services.securities_by_sector');

        $count = $securitiesService
            ->count($sector);

        $totalRaised = $securitiesService
            ->sum($sector);

        $securities = $securitiesService
            ->findNextMaturing($sector, 2);

        $securityPresenters = [];
        if (!empty($securities)) {
            foreach ($securities as $security) {
                $securityPresenters[] = new SecurityPresenter($security, [
                    'template' => 'simple',
                ]);
            }
        }

        $this->setTitle($sector->getName());
        $this->toView('totalRaised', new MoneyPresenter($totalRaised, ['scale' => true]));
        $this->toView('count', number_format($count));
        $this->toView('securities', $securityPresenters);
        $this->toView('hasSecurities', $count > 0);
        $this->toView('entityNav', new EntityNavPresenter($sector, 'show'));

        return $this->renderTemplate('sectors:show');
    }

    public function maturityProfileAction(Request $request)
    {
        throw new HttpException(404, 'Not yet');
//        $this->setTitle('Issuance ' . $year . ' - ' . $sector->getName());
//        $this->toView('entityNav', new EntityNavPresenter($sector, 'maturity_profile'));
//        return $this->renderTemplate('sectors:maturity-profile');
    }

    public function issuanceAction(Request $request)
    {
        $sector = $this->getSector($request);
        return $this->renderIssuance($request, $sector);
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

        $this->setTitle('Securities - ' . $sector->getName());
        $this->toView('totalRaised', new MoneyPresenter($totalRaised, ['scale' => true]));
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

        $this->toView('sector', $sector);
        $this->toView('entityContextPresenter', new EntityContextPresenter($sector));

        // I'm looking at a sector, so I need to pass in that sector,
        // and it's parent industry
        $this->setFinder($request->get('_route'), $industry, $sector);

        return $sector;
    }
}
