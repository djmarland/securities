<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits;
use AppBundle\Presenter\Organism\EntityContext\EntityContextPresenter;
use AppBundle\Presenter\Organism\Industry\IndustryPresenter;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\Exception\ValidationException;
use SecuritiesService\Domain\ValueObject\UUID;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class IndustriesController extends Controller
{
    use Traits\MaturityProfileTrait;
    use Traits\SecuritiesTrait;
    use Traits\IssuanceTrait;
    use Traits\OverviewTrait;
    use Traits\FinderTrait;

    public function initialize(Request $request)
    {
        parent::initialize($request);
        $this->toView('currentSection', 'industries');
    }

    public function listAction()
    {
        $sectors = $this->get('app.services.sectors')
            ->findAllInIndustries();

        $industryPresenters = [];
        $prevIndustry = null;
        $collectedSectors = [];
        if (!empty($sectors)) {
            foreach ($sectors as $sector) {
                $industry = $sector->getIndustry();
                if ($industry != $prevIndustry) {
                    if ($prevIndustry) {
                        $industryPresenters[] = new IndustryPresenter($prevIndustry, $collectedSectors);
                    }
                    $prevIndustry = $industry;
                    $collectedSectors = [];
                }
                $collectedSectors[] = $sector;
            }
            if ($prevIndustry) {
                $industryPresenters[] = new IndustryPresenter($prevIndustry, $collectedSectors);
            }
        }

        $this->setTitle('Industries');
        $this->toView('industries', $industryPresenters);

        return $this->renderTemplate('industries:list');
    }

    public function showAction(Request $request)
    {
        $industry = $this->getIndustry($request);
        return $this->renderOverview($request, $industry);
    }

    public function securitiesAction(Request $request)
    {
        $industry = $this->getIndustry($request);
        return $this->renderSecurities($request, $industry);
    }

    public function maturityProfileAction(Request $request)
    {
        $industry = $this->getIndustry($request);
        return $this->renderMaturityProfile($request, $industry);
    }

    public function issuanceAction(Request $request)
    {
        $industry = $this->getIndustry($request);
        return $this->renderIssuance($request, $industry);
    }

    private function getIndustry(Request $request)
    {
        $id = $request->get('industry_id');

        try {
            $industry = $this->get('app.services.industries')
                ->findByUUID(UUID::createFromString($id));
        } catch (ValidationException $e) {
            throw new HttpException(404, $e->getMessage());
        } catch (EntityNotFoundException $e) {
            throw new HttpException(404, $e->getMessage());
        }

        $this->toView('industry', $industry);
        $this->toView('entityContextPresenter', new EntityContextPresenter($industry));

        // I'm looking at an industry, so I need to pass in that industry
        $this->setFinder($request->get('_route'), $industry);

        return $industry;
    }
}
