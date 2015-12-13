<?php

namespace AppBundle\Controller;

use AppBundle\Presenter\Organism\Issuer\IssuerPresenter;
use AppBundle\Presenter\Organism\Security\SecurityPresenter;
use SecuritiesService\Domain\ValueObject\ID;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class IssuersController extends Controller
{
    public function initialize(Request $request)
    {
        parent::initialize($request);
        $this->toView('currentSection', 'issuers');
    }

    public function listAction()
    {
        $perPage = 1500;
        $currentPage = $this->getCurrentPage();

        $result = $this->get('app.services.issuers')
            ->findAndCountAll($perPage, $currentPage);

        $issuerPresenters = [];
        $issuers = $result->getDomainModels();
        if (!empty($issuers)) {
            foreach ($issuers as $issuer) {
                $issuerPresenters[] = new IssuerPresenter($issuer);
            }
        }

        $this->setTitle('Issuers');
        $this->toView('issuers', $issuerPresenters);
        $this->toView('total', $result->getTotal());

        $this->setPagination(
            $result->getTotal(),
            $currentPage,
            $perPage
        );

        return $this->renderTemplate('issuers:list');
    }

    public function showAction(Request $request)
    {
        $id = $request->get('id');
        $result = $this->get('app.services.issuers')
            ->findByID(new ID((int) $id));

        $issuer = $result->getDomainModel();
        if (!$issuer) {
            throw new HttpException(404, 'Issuer ' . $id . ' does not exist.');
        }

        $this->setTitle($issuer->getName());
        $this->toView('issuer', $issuer);

        $perPage = 50;
        $currentPage = $this->getCurrentPage();

        $result = $this->get('app.services.securities')
            ->findAndCountByIssuer($issuer, $perPage, $currentPage);

        $securityPresenters = [];
        $securities = $result->getDomainModels();
        if (!empty($securities)) {
            foreach ($securities as $security) {
                $securityPresenters[] = new SecurityPresenter($security);
            }
        }

        $this->toView('securities', $securityPresenters);
        $this->toView('total', $result->getTotal());

        $this->setPagination(
            $result->getTotal(),
            $currentPage,
            $perPage
        );

        return $this->renderTemplate('issuers:show');
    }
}
