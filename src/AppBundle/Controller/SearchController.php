<?php

namespace AppBundle\Controller;

use AppBundle\Presenter\Organism\Issuer\IssuerPresenter;
use AppBundle\Presenter\Organism\Security\SecurityPresenter;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\Exception\ValidationException;
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

                if ($single) {
                    // if there was an exact match, just send you straight there
                    return $this->redirectToRoute(
                        'security_show',
                        ['isin' => $single->getIsin()]
                    );
                }

            } catch (ValidationException $e) {
                // the given query was not an ISIN, move on
            } catch (EntityNotFoundException $e) {
                // ISIN was not found, move on
            }


            $securities = $this->get('app.services.securities_search')
                ->byName($query, 100, 1);

            $securityPresenters = [];
            if (!empty($securities)) {
                foreach ($securities as $security) {
                    $securityPresenters[] = new SecurityPresenter($security, [
                        'template' => 'simple',
                    ]);
                }
            }

            $this->toView('securities', $securityPresenters);
            $this->toView('hasSecurities', !empty($securities));

            $issuers = $this->get('app.services.issuers')
                ->search($query, 100, 1);

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
