<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\FinderTrait;
use AppBundle\Controller\Traits\IssuanceTrait;
use AppBundle\Controller\Traits\OverviewTrait;
use AppBundle\Controller\Traits\SecuritiesTrait;
use AppBundle\Presenter\Organism\EntityContext\EntityContextPresenter;
use Symfony\Component\HttpFoundation\Request;

class OverviewController extends Controller
{
    use IssuanceTrait;
    use OverviewTrait;
    use SecuritiesTrait;
    use FinderTrait;

    public function initialize(Request $request)
    {
        parent::initialize($request);
        $this->toView('currentSection', 'overview');
        $this->toView('entityContextPresenter', new EntityContextPresenter(null));
        $this->setFinder($request->get('_route'));
    }

    public function showAction(Request $request)
    {
        return $this->renderOverview($request);
    }

    public function issuanceAction(Request $request)
    {
        return $this->renderIssuance($request);
    }

    public function securitiesAction(Request $request)
    {
        return $this->renderSecurities($request);
    }
}
