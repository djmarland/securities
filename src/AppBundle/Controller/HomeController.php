<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Entity\Security;
use AppBundle\Domain\ValueObject\{ID, ISIN};

class HomeController extends Controller
{
    public function indexAction()
    {
        $this->toView('searchAutofocus', 'autofocus');

        $securitiesCount = $this->get('app.services.securities')->countAll();
        $issuersCount = $this->get('app.services.issuers')->countAll();

        $this->toView('securitiesCount', number_format($securitiesCount));
        $this->toView('issuersCount', number_format($issuersCount));

        $this->toView('byProduct', [

        ]);
        return $this->renderTemplate('home:index');
    }

    public function styleguideAction()
    {
        return $this->renderTemplate('home:styleguide');
    }
}
