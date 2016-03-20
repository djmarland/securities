<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\FinderTrait;
use AppBundle\Controller\Traits\IssuanceTrait;
use AppBundle\Presenter\Organism\EntityContext\EntityContextPresenter;
use Symfony\Component\HttpFoundation\Request;

class OverviewController extends Controller
{
    use IssuanceTrait;
    use FinderTrait;

    public function initialize(Request $request)
    {
        parent::initialize($request);
        $this->toView('currentSection', 'overview');
        $this->toView('entityContextPresenter', new EntityContextPresenter(null));
        $this->setFinder();
    }

    public function showAction(Request $request)
    {
        $securitiesCount = $this->get('app.services.securities')->countAll();
        $securitiesSum = $this->get('app.services.securities')->sumAll();

        $this->toView('securitiesCount', number_format($securitiesCount));
        $this->toView('securitiesSum', number_format($securitiesSum));
        $this->toView('activeTab', 'overview');

        return $this->renderTemplate('overview:show');
    }

    public function issuanceAction(Request $request)
    {
        return $this->renderIssuance($request);
    }
}
