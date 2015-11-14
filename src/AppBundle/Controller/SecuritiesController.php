<?php

namespace AppBundle\Controller;

use AppBundle\Domain\ValueObject\ISIN;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SecuritiesController extends Controller
{
    public function listAction()
    {
        $perPage = 50;
        $currentPage = $this->getCurrentPage();

        $result = $this->get('app.services.securities')
            ->findAndCountLatest($perPage, $currentPage);

        $this->toView('securities', $result->getDomainModels());
        $this->toView('total', $result->getTotal());

        $this->setPagination(
            $result->getTotal(),
            $currentPage,
            $perPage
        );

        return $this->renderTemplate('securities:list');
    }

    public function showAction(Request $request)
    {
        $isin = $request->get('isin');
        $result = $this->get('app.services.securities')
            ->findByIsin(new ISIN($isin));

        $security = $result->getDomainModel();
        if (!$security) {
            throw new HttpException(404, 'Security ' . $isin . ' does not exist.');
        }

        $this->toView('security', $security);
        return $this->renderTemplate('securities:show');
    }
}
