<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Entity\Security;
use AppBundle\Domain\ValueObject\{ID, ISIN};

class HomeController extends Controller
{
    public function indexAction()
    {
        $i1 = new Security(
            new ID(1),
            new \DateTime(),
            new \DateTime(),
            new ISIN('XS1170304742'),
            'FRN 02/07/19 SEK1000000'
        );

        $i2 = new Security(
            new ID(2),
            new \DateTime(),
            new \DateTime(),
            new ISIN('XS1176580741'),
            'FLTG RT CVD BDS 10/09/18 SEK1000000'
        );

        $this->toView('securities', [$i1, $i2]);


        return $this->renderTemplate('home:index');
    }

    public function styleguideAction()
    {
        return $this->renderTemplate('home:styleguide');
    }
}
