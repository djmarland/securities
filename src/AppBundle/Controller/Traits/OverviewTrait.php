<?php

namespace AppBundle\Controller\Traits;

use AppBundle\Presenter\Molecule\Money\MoneyPresenter;
use AppBundle\Presenter\Organism\EntityNav\EntityNavPresenter;
use AppBundle\Presenter\Organism\Security\SecurityPresenter;
use SecuritiesService\Domain\Entity\Entity;
use Symfony\Component\HttpFoundation\Request;

trait OverviewTrait
{
    protected function renderOverview(
        Request $request,
        Entity $entity = null
    ) {
        if ($entity) {
            $entityType = $entity->getRoutePrefix();
            $securitiesService = $this->get('app.services.securities_by_' . $entityType);
            $securitiesService->setDomainEntity($entity);
            $this->setTitle($entity->getName());
            $this->toView(
                'allSecuritiesPath',
                $this->generateUrl(
                    $entityType . '_securities',
                    [
                        $entityType . '_id' => $entity->getId()
                    ]
                )
            );
        } else {
            $securitiesService = $this->get('app.services.securities');
            $this->setTitle('Overview');
            $this->toView('allSecuritiesPath', $this->generateUrl('overview_securities'));
        }

        $count = $securitiesService->count();

        $totalRaised = $securitiesService->sum();

        $securities = $securitiesService->findNextMaturing(2);

        $securityPresenters = [];
        if (!empty($securities)) {
            foreach ($securities as $security) {
                $securityPresenters[] = new SecurityPresenter($security, [
                    'template' => 'simple',
                ]);
            }
        }

        $this->toView('totalRaised', new MoneyPresenter($totalRaised, ['scale' => true]));
        $this->toView('count', number_format($count));
        $this->toView('securities', $securityPresenters);
        $this->toView('hasSecurities', $count > 0);
        $this->toView('entityNav', new EntityNavPresenter($entity, 'show'));
        return $this->renderTemplate('entities:overview');
    }
}