<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Entity\Security;
use AppBundle\Domain\ValueObject\{ID, ISIN};
use AppBundle\Presenter\Organism\Security\SecurityPresenter;

class HomeController extends Controller
{
    public function indexAction()
    {
        $this->toView('searchAutofocus', 'autofocus');

        $securitiesCount = $this->get('app.services.securities')->countAll();
        $issuersCount = $this->get('app.services.issuers')->countAll();
        $productCounts = $this->get('app.services.securities')->countsByProduct();

        $this->toView('securitiesCount', number_format($securitiesCount));
        $this->toView('issuersCount', number_format($issuersCount));


        $upcomingResult = $this->get('app.services.securities')->findUpcomingMaturities(new \DateTimeImmutable(), 5);
        $securityPresenters = [];
        $securities = $upcomingResult->getDomainModels();
        if (!empty($securities)) {
            foreach ($securities as $security) {
                $securityPresenters[] = new SecurityPresenter($security);
            }
        }

        $this->toView('securities', $securityPresenters);

        $byProduct = [
            ['Funding Product', 'Number']
        ];
        foreach ($productCounts as $pc) {
            $byProduct[] = [
                $pc->product->getName(),
                $pc->count
            ];
        }
        $this->toView('byProduct', $byProduct);
        return $this->renderTemplate('home:index');
    }

    public function styleguideAction()
    {
        return $this->renderTemplate('home:styleguide');
    }
}
