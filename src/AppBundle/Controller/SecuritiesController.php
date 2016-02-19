<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\Finder;
use AppBundle\Controller\Traits\SecurityFilter;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\ValueObject\ISIN;
use AppBundle\Presenter\Organism\Security\SecurityPresenter;
use SecuritiesService\Service\Filter\SecuritiesFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SecuritiesController extends Controller
{
    use SecurityFilter;
    use Finder;

    public function initialize(Request $request)
    {
        parent::initialize($request);
        $this->toView('currentSection', 'securities');
    }

    public function listAction(Request $request)
    {
        $perPage = 50;
        $currentPage = $this->getCurrentPage();


        $filter = new SecuritiesFilter(
            $this->setProductFilter($request),
            $this->setCurrencyFilter($request),
            $this->setBucketFilter($request)
        );

        $total = $this->get('app.services.securities')
            ->countAllFiltered(
                $filter
            );

        $securities = [];
        if ($total) {
            $securities = $this->get('app.services.securities')
                ->findAllFiltered(
                    $filter,
                    $perPage,
                    $currentPage
                );
        }

        $securityPresenters = [];
        if (!empty($securities)) {
            foreach ($securities as $security) {
                $securityPresenters[] = new SecurityPresenter($security);
            }
        }

        $this->setTitle('Securities');
        $this->toView('securities', $securityPresenters);
        $this->toView('total', $total);

        $this->setPagination(
            $total,
            $currentPage,
            $perPage
        );

        $this->setFinder();

        return $this->renderTemplate('securities:list');
    }

    public function showAction(Request $request)
    {
        $isin = $request->get('isin');

        try {
            $security = $this->get('app.services.securities')
                ->findByIsin(new ISIN($isin));
        } catch (EntityNotFoundException $e) {
            throw new HttpException(404, 'Security ' . $isin . ' does not exist.');
        }

        $this->setTitle($security->getISIN());
        $this->toView('security', new SecurityPresenter(
            $security,
            [
                'showTitle' => false,
                'includeLink' => false
            ]
        ));
        return $this->renderTemplate('securities:show');
    }
}
