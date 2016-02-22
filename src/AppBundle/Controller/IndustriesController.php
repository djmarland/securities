<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\FinderTrait;
use AppBundle\Controller\Traits\SecurityFilterTrait;
use AppBundle\Presenter\Organism\EntityNav\EntityNavPresenter;
use AppBundle\Presenter\Organism\Industry\IndustryPresenter;
use AppBundle\Presenter\Organism\Issuance\IssuanceGraphPresenter;
use AppBundle\Presenter\Organism\Issuance\IssuanceTablePresenter;
use AppBundle\Presenter\Organism\Security\SecurityPresenter;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\Exception\ValidationException;
use SecuritiesService\Domain\ValueObject\UUID;
use SecuritiesService\Service\Filter\SecuritiesFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class IndustriesController extends Controller
{
    use SecurityFilterTrait;
    use FinderTrait;

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

        $securitiesService = $this->get('app.services.securities_by_industry');

        $count = $securitiesService
            ->count($industry);

        $totalRaised = $securitiesService
            ->sum($industry);

        $securities = $securitiesService
            ->findNextMaturing($industry, 2);

        $securityPresenters = [];
        if (!empty($securities)) {
            foreach ($securities as $security) {
                $securityPresenters[] = new SecurityPresenter($security, [
                    'template' => 'simple',
                ]);
            }
        }

        $this->toView('totalRaised', number_format($totalRaised));
        $this->toView('count', number_format($count));
        $this->toView('securities', $securityPresenters);
        $this->toView('hasSecurities', $count > 0);
        $this->toView('entityNav', new EntityNavPresenter($industry, 'show'));
        return $this->renderTemplate('industries:show');
    }

    public function securitiesAction(Request $request)
    {
        $industry = $this->getIndustry($request);

        $filter = $this->setFilter($request);

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

        $this->toView('entityNav', new EntityNavPresenter($industry, 'securities'));
        return $this->renderTemplate('industries:securities');
    }

    public function maturityProfileAction(Request $request)
    {
        throw new HttpException(404, 'Not yet');
//        $this->toView('entityNav', new EntityNavPresenter($industry, 'maturity_profile'));
//        return $this->renderTemplate('groups:maturity-profile');
    }

    public function issuanceAction(Request $request)
    {
        $industry = $this->getIndustry($request);
        $years = $this->get('app.services.securities_by_industry')->issuanceYears($industry);

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
                    'industry_issuance',
                    [
                        'industry_id' => $industry->getId(),
                        'year' => $year,
                    ]
                )
            );
        }

        $this->toView('activeYear', $year);
        $this->toView('years', $years);
        $this->toView('entityNav', new EntityNavPresenter($industry, 'issuance'));

        $results = [];
        if ($year) {
            $results = $this->get('app.services.securities_by_industry')->productCountsByMonthForYear(
                $industry,
                $year
            );
        }

        $hasData = false;
        $issuanceTable = null;
        $issuanceGraph = null;
        if (!empty($results)) {
            $hasData = true;
            $issuanceTable = new IssuanceTablePresenter($industry, $results, $year);
            $issuanceGraph = new IssuanceGraphPresenter($industry, $results, $year);
        }

        $this->toView('hasData', $hasData);
        $this->toView('issuanceTable', $issuanceTable);
        $this->toView('issuanceGraph', $issuanceGraph);

        return $this->renderTemplate('industries:issuance');
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

        $this->setTitle($industry->getName());
        $this->toView('industry', $industry);


        // I'm looking at an industry, so I need to pass in that industry
        $this->setFinder($request->get('_route'), $industry);

        return $industry;
    }
}
