<?php

namespace AppBundle\Controller;

use AppBundle\Presenter\Organism\Issuer\IssuerPresenter;
use AppBundle\Presenter\Organism\Security\SecurityPresenter;
use InvalidArgumentException;
use SecuritiesService\Domain\ValueObject\ISIN;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    public function initialize(Request $request)
    {
        parent::initialize($request);
        $this->toView('currentSection', 'search');
    }

    public function listAction()
    {
        $query = $this->request->get('q', null);
        $this->setTitle('Search');

        if ($query) {
            $this->setTitle('Search - ' . $query);

            try {

                $isin = new ISIN($query);

                $single = $this->get('app.services.securities')
                    ->findByIsin($isin);

                if ($single->hasResult()) {
                    // if there was an exact match, just send you straight there
                    return $this->redirectToRoute(
                        'securities_show',
                        [
                            'isin' => $single->getDomainModel()->getIsin()
                        ]
                    );
                }

            } catch (InvalidArgumentException $e) {
                // the given query was not an ISIN, move on
            }


            $securities = $this->get('app.services.securities_search')
                ->byName($query, 20, 1);

            $securityPresenters = [];
            if (!empty($securities)) {
                foreach ($securities as $security) {
                    $securityPresenters[] = new SecurityPresenter($security);
                }
            }

            $this->toView('securities', $securityPresenters);
            $this->toView('hasSecurities',!empty($securities));

            $issuers = $this->get('app.services.issuers')
                ->search($query, 20, 1);

            $issuerPresenters = [];
            if (!empty($issuers)) {
                foreach ($issuers as $issuer) {
                    $issuerPresenters[] = new IssuerPresenter($issuer);
                }
            }

            $this->toView('issuers', $issuerPresenters);
            $this->toView('hasIssuers', !empty($issuers));

            return $this->renderTemplate('search:list');
        }

        // @todo - advanced search
        return $this->renderTemplate('search:index');
    }
}
