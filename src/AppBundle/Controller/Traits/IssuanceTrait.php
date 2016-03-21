<?php

namespace AppBundle\Controller\Traits;

use AppBundle\Presenter\Organism\EntityNav\EntityNavPresenter;
use AppBundle\Presenter\Organism\Issuance\IssuanceGraphPresenter;
use AppBundle\Presenter\Organism\Issuance\IssuanceTablePresenter;
use SecuritiesService\Domain\Entity\Entity;
use Symfony\Component\HttpFoundation\Request;

trait IssuanceTrait
{
    protected function renderIssuance(
        Request $request,
        Entity $entity = null
    ) {
        if ($entity) {
            $entityType = $entity->getRoutePrefix();
            $securitiesService = $this->get('app.services.securities_by_' . $entityType);
            $securitiesService->setDomainEntity($entity);
            $this->setTitle('Issuance  - ' . $entity->getName());
        } else {
            $securitiesService = $this->get('app.services.securities');
            $this->setTitle('Issuance');
        }

        $currentYear = (int) $this->getApplicationTime()->format('Y');
        $lastYear = $currentYear - 1;
        $results = [];

        $results[$currentYear] = $securitiesService->sumByMonthForYear($currentYear, $entity);
        $results[$lastYear] = $securitiesService->sumByMonthForYear($lastYear, $entity);

        $hasData = false;
        $issuanceTable = null;
        $issuanceGraph = null;
        if (!empty($results[$currentYear]) || !empty($results[$lastYear])) {
            $hasData = true;
            $issuanceTable = new IssuanceTablePresenter($entity, $results);
            $issuanceGraph = new IssuanceGraphPresenter($entity, $results);
        }

        $this->toView('hasData', $hasData);
        $this->toView('issuanceTable', $issuanceTable);
        $this->toView('issuanceGraph', $issuanceGraph);
        $this->toView('entity', $entity);
        $this->toView('entityNav', new EntityNavPresenter($entity, 'issuance'));

        return $this->renderTemplate('entities:issuance');
    }
}
