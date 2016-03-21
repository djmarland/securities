<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\FinderTrait;
use AppBundle\Controller\Traits\IssuanceTrait;
use AppBundle\Controller\Traits\OverviewTrait;
use AppBundle\Controller\Traits\SecuritiesTrait;
use AppBundle\Presenter\Molecule\Money\MoneyPresenter;
use AppBundle\Presenter\Organism\EntityContext\EntityContextPresenter;
use AppBundle\Presenter\Organism\EntityNav\EntityNavPresenter;
use AppBundle\Presenter\Organism\Sector\SectorPresenter;
use AppBundle\Presenter\Organism\Security\SecurityPresenter;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\Exception\ValidationException;
use SecuritiesService\Domain\ValueObject\UUID;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SectorsController extends Controller
{
    use SecuritiesTrait;
    use IssuanceTrait;
    use OverviewTrait;
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
        return $this->renderOverview($request, $sector);
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
        return $this->renderSecurities($request, $sector);
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
