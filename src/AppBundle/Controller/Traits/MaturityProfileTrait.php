<?php

namespace AppBundle\Controller\Traits;

use AppBundle\Presenter\Organism\EntityNav\EntityNavPresenter;
use AppBundle\Presenter\Organism\MaturityProfile\MaturityProfilePresenter;
use SecuritiesService\Domain\Entity\Entity;
use Symfony\Component\HttpFoundation\Request;

trait MaturityProfileTrait
{
    protected function renderMaturityProfile(
        Request $request,
        Entity $entity = null
    ) {
        
        if ($entity) {
            $entityType = $entity->getRoutePrefix();
            $securitiesService = $this->get('app.services.securities_by_' . $entityType);
            $securitiesService->setDomainEntity($entity);
            $this->titleParts[] = $entity->getName();
        } else {
            $securitiesService = $this->get('app.services.securities');
        }

        $results = [];
        $buckets = $this->get('app.services.buckets')->getAll();
        foreach ($buckets as $bucket) {
            $results[] = (object) [
                'sums' => $securitiesService->sumByProductForBucket($bucket),
                'bucket' => $bucket,
            ];
        }

        $this->toView(
            'maturityProfilePresenter',
            new MaturityProfilePresenter($entity, $results)
        );

        $this->toView('entity', $entity);
        $this->toView('entityNav', new EntityNavPresenter($entity, 'issuance'));
        return $this->renderTemplate('entities:maturity-profile');
    }
}
