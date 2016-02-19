<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\Finder;
use AppBundle\Controller\Traits\SecurityFilter;
use AppBundle\Presenter\Organism\Industry\IndustryPresenter;
use AppBundle\Presenter\Organism\Security\SecurityPresenter;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\ValueObject\ID;
use SecuritiesService\Service\Filter\SecuritiesFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class IndustriesController extends Controller
{
    use SecurityFilter;
    use Finder;

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



        return $this->renderTemplate('industries:show');
    }

    public function securitiesAction(Request $request)
    {
        $industry = $this->getIndustry($request);

        $filter = new SecuritiesFilter(
            $this->setProductFilter($request),
            $this->setCurrencyFilter($request),
            $this->setBucketFilter($request)
        );

        $perPage = 50;
        $currentPage = $this->getCurrentPage();

        $securitiesService = $this->get('app.services.securities_by_industry');
        $total = $securitiesService->count($industry, $filter);
        $totalRaised = 0;
        $securities = [];
        if ($total) {
            $securities = $securitiesService
                ->find(
                    $industry,
                    $filter,
                    $perPage,
                    $currentPage
                );
            $totalRaised = $securitiesService->sum($industry, $filter);
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

        return $this->renderTemplate('industries:securities');
    }

    private function getIndustry(Request $request)
    {
        $id = $request->get('industry_id');

        if ($id !== (string) (int) $id) {
            throw new HttpException(404, 'Invalid ID');
        }

        try {
            $industry = $this->get('app.services.industries')
                ->findByID(new ID((int)$id));
        } catch (EntityNotFoundException $e) {
            throw new HttpException(404, 'Industry ' . $id . ' does not exist.');
        }

        $this->setTitle($industry->getName());
        $this->toView('industry', $industry);


        // I'm looking at an industry, so I need to pass in that industry
        $this->setFinder($industry);

        return $industry;
    }
}