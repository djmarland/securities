<?php

namespace AppBundle\Presenter\Organism\EntityContext;

use AppBundle\Presenter\Presenter;
use SecuritiesService\Domain\Entity\Company;
use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\Industry;
use SecuritiesService\Domain\Entity\ParentGroup;
use SecuritiesService\Domain\Entity\Sector;

class EntityContextPresenter extends Presenter implements EntityContextPresenterInterface
{
    private $parents = [];

    public function __construct(
        Entity $entity = null,
        array $options = []
    ) {
        parent::__construct($entity, $options);

        if ($entity) {
            // get the whole stack of parents
            $stack = $this->getStack($entity);

            // reverse the stack so the highest is top
            $stack = array_reverse($stack);

            // trim the last thing off the stack (as it'll be shown below)
            array_pop($stack);

            // set as the parents list
            $this->parents = $stack;
        }
    }

    public function isVisible(): bool
    {
        return !!$this->domainModel;
    }

    public function hasParents(): bool
    {
        return !empty($this->parents);
    }

    public function getParents(): array
    {
        return $this->parents;
    }

    public function getEntityName(): string
    {
        if ($this->domainModel) {
            return $this->domainModel->getName();
        }
        return '';
    }

    private function getStack($entity, $parents = []): array
    {
        $parents[] = $this->stackObject($entity->getRoutePrefix(), $entity->getId(), $entity->getName());

        if ($entity instanceof Company) {
            $group = $entity->getParentGroup();
            if ($group) {
                return $this->getStack($group, $parents);
            }
        }

        if ($entity instanceof ParentGroup) {
            $sector = $entity->getSector();
            if ($sector) {
                return $this->getStack($sector, $parents);
            }
        }

        if ($entity instanceof Sector) {
            $industry = $entity->getIndustry();
            if ($industry) {
                return $this->getStack($industry, $parents);
            }
        }

        return $parents;
    }

    private function stackObject($prefix, $id, $name): \stdClass
    {
        return (object) [
            'route' => $prefix . '_show',
            'params' => [
                $prefix . '_id' => $id,
            ],
            'name' => $name,
        ];
    }
}