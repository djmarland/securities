<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Entity\Security;
use AppBundle\Domain\ValueObject\{ID, ISIN};

class HomeController extends Controller
{
    public function indexAction()
    {
        $this->toView('showMasthead', false);
        $this->toView('searchAutofocus', 'autofocus');


        return $this->renderTemplate('home:index');
    }

    public function styleguideAction()
    {
        return $this->renderTemplate('home:styleguide');
    }
}
