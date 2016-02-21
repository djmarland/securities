<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\FinderTrait;
use Symfony\Component\HttpFoundation\Request;

class OverviewController extends Controller
{
    use FinderTrait;

    public function initialize(Request $request)
    {
        parent::initialize($request);
        $this->toView('currentSection', 'overview');
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
}
