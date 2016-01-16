<?php

namespace AppBundle\Controller;

use AppBundle\Presenter\Organism\Security\SecurityPresenter;
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
        $perPage = 50;
        $currentPage = $this->getCurrentPage();

        $query = $this->request->get('q', null);
        $this->setTitle('Search');

        if ($query) {
            $this->setTitle('Search - ' . $query);

            $result = $this->get('app.services.securities')
                ->searchAndCount($query, $perPage, $currentPage);

            $securityPresenters = [];
            $securities = $result->getDomainModels();
            if (!empty($securities)) {
                foreach ($securities as $security) {
                    $securityPresenters[] = new SecurityPresenter($security);
                }
            }

            $this->toView('securities', $securityPresenters);
            $this->toView('total', $result->getTotal());
            $this->toView('hasResults', $result->getTotal() > 0);

            $this->setPagination(
                $result->getTotal(),
                $currentPage,
                $perPage
            );
            return $this->renderTemplate('search:list');
        }

        // @todo - advanced search
        return $this->renderTemplate('search:index');
    }
}
