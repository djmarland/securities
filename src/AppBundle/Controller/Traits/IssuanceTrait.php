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
        $isYearToDate = ($request->get('view') == 'ytd');
        $titleSuffix = $isYearToDate ? 'Year to Date' : 'Monthly';
        $this->toView('issuanceView', $isYearToDate ? 'ytd' : 'monthly');

        if ($entity) {
            $entityType = $entity->getRoutePrefix();
            $securitiesService = $this->get('app.services.securities_by_' . $entityType);
            $securitiesService->setDomainEntity($entity);
            $title = 'Issuance  - ' . $titleSuffix . ' - ' . $entity->getName();
            $this->toView(
                'monthlyPath',
                $this->generateUrl(
                    $entityType . '_issuance',
                    [$entityType . '_id' => $entity->getId()]
                )
            );
            $this->toView(
                'ytdPath',
                $this->generateUrl(
                    $entityType . '_issuance',
                    [
                        'view' => 'ytd',
                        $entityType . '_id' => $entity->getId(),
                    ]
                )
            );
        } else {
            $securitiesService = $this->get('app.services.securities');
            $title = 'Issuance  - ' . $titleSuffix;
            $this->toView(
                'monthlyPath',
                $this->generateUrl('overview_issuance')
            );
            $this->toView(
                'ytdPath',
                $this->generateUrl('overview_issuance', ['view' => 'ytd'])
            );
        }
        $this->toView('pageTitle', 'Issuance  - ' . $titleSuffix);

        $currentYear = (int) $this->getApplicationTime()->format('Y');
        $lastYear = $currentYear - 1;
        $oldYear = $lastYear - 1;
        $results = [];

//        $results[$oldYear] = $securitiesService->sumByMonthForYear($oldYear, $entity);
//        $results[$lastYear] = $securitiesService->sumByMonthForYear($lastYear, $entity);
        $results[$currentYear] = $securitiesService->sumByMonthForYear($currentYear, $entity);

        $hasData = false;
        $options = ['cumulative' => $isYearToDate];
        $issuanceTable = null;
        $issuanceGraph = null;
        if (!empty($results[$currentYear]) || !empty($results[$lastYear])) {
            $hasData = true;
            $issuanceTable = new IssuanceTablePresenter($entity, $results, $options);
            $issuanceGraph = new IssuanceGraphPresenter($entity, $results, $options);
        }

        $this->toView('hasData', $hasData);
        $this->toView('issuanceTable', $issuanceTable);
        $this->toView('issuanceGraph', $issuanceGraph);
        $this->toView('entity', $entity);
        $this->toView('entityNav', new EntityNavPresenter($entity, 'issuance'));

        return $this->renderTemplate('entities:issuance', $title);
    }
}
