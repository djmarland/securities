<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\Finder;
use AppBundle\Controller\Traits\SecurityFilter;
use SecuritiesService\Domain\ValueObject\ISIN;
use AppBundle\Presenter\Organism\Security\SecurityPresenter;
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

        $product = $this->setProductFilter($request);
        $currency = $this->setCurrencyFilter($request);
        $bucket = $this->setBucketFilter($request);

        $result = $this->get('app.services.securities')
            ->findAndCountAllWithFilters(
                $perPage,
                $currentPage,
                $product,
                $currency,
                null,
                $bucket
            );

        $securityPresenters = [];
        $securities = $result->getDomainModels();
        if (!empty($securities)) {
            foreach ($securities as $security) {
                $securityPresenters[] = new SecurityPresenter($security);
            }
        }

        $this->setTitle('Securities');
        $this->toView('securities', $securityPresenters);
        $this->toView('total', $result->getTotal());

        $this->setPagination(
            $result->getTotal(),
            $currentPage,
            $perPage
        );

        $this->setFinder();

        return $this->renderTemplate('securities:list');
    }

    public function showAction(Request $request)
    {
        $isin = $request->get('isin');
        $result = $this->get('app.services.securities')
            ->findByIsin(new ISIN($isin));

        $security = $result->getDomainModel();
        if (!$security) {
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
