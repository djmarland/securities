<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\FinderTrait;
use AppBundle\Controller\Traits\SecuritiesTrait;
use AppBundle\Presenter\Molecule\Money\MoneyPresenter;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\ValueObject\ISIN;
use AppBundle\Presenter\Organism\Security\SecurityPresenter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SecuritiesController extends Controller
{
    use FinderTrait;

    public function initialize(Request $request)
    {
        parent::initialize($request);
        $this->toView('currentSection', 'securities');
    }

    public function showAction(Request $request)
    {
        $isin = $request->get('isin');
        $upper = strtoupper($isin);

        if ($isin !== $upper) {
            return $this->redirectToRoute('security_show', ['isin' => $upper], 301);
        }

        try {
            $security = $this->get('app.services.securities')
                ->fetchByIsin(new ISIN($isin));
        } catch (EntityNotFoundException $e) {
            throw new HttpException(404, 'Security ' . $isin . ' does not exist.');
        }

        $this->toView('issuer', null);
        $this->toView('byProduct', null);

        $group = null;

        $title = $security->getISIN();
        if ($security->getCompany()) {
            $title .= ' - ' . $security->getCompany()->getName();
            $this->toView('issuer', $security->getCompany());

            if ($security->getCompany()->getParentGroup()) {
                $group = $security->getCompany()->getParentGroup();
            }
        }

        $this->toView('product', null);
        if ($security->getProduct()) {
            $this->toView('product', $security->getProduct());
        }

        $this->toView('group', null);
        if ($group) {
            $groupSecuritiesService = $this->get('app.services.securities_by_group');
            $groupSecuritiesService->setDomainEntity($group);

            $productCounts = $groupSecuritiesService->countsByProduct();

            $byProduct = (object) [
                'headings' => [],
                'counts' => []
            ];

            foreach ($productCounts as $pc) {
                $byProduct->headings[] = $pc->product->getName();
                $byProduct->counts[] = $pc->count;
            }
            $this->toView('byProduct', $byProduct);
            $this->toView('group', $group);
            $this->toView('groupCount', number_format($groupSecuritiesService->count()));
            $this->toView('groupAmount', new MoneyPresenter($groupSecuritiesService->sum(), ['scale' => true]));
        }

        $this->setTitle($title);
        $this->toView('security', $security, true);
        $this->toView('securityPresenter', new SecurityPresenter($security, ['template' => 'full']));
        return $this->renderTemplate('securities:show');
    }
}
