<?php

namespace AppBundle\Controller;

use AppBundle\Presenter\Organism\Issuer\IssuerPresenter;
use AppBundle\Presenter\Organism\Security\SecurityPresenter;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\Exception\ValidationException;
use SecuritiesService\Domain\ValueObject\ISIN;
use Symfony\Component\HttpFoundation\Request;

class CountriesController extends Controller
{
    public function initialize(Request $request)
    {
        parent::initialize($request);
        $this->toView('currentSection', 'countries');
    }

    public function listAction()
    {
        $query = $this->request->get('q', null);
        $this->setTitle('Countries');

        $countries = $this->get('app.services.countries')
            ->findAll();

        $this->toView('countries', $countries, true);

        return $this->renderTemplate('');
    }
}
