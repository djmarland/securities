<?php

namespace AppBundle\Presenter\Organism\EntityNav;

use AppBundle\Presenter\Presenter;
use SecuritiesService\Domain\Entity\Company;
use SecuritiesService\Domain\Entity\Industry;
use SecuritiesService\Domain\Entity\ParentGroup;
use SecuritiesService\Domain\Entity\Sector;

class EntityNavPresenter extends Presenter implements EntityNavPresenterInterface
{
    private $currentPage;

    private $tabs = [
        'show' => [
            'text' => 'Overview',
        ],
        'securities' => [
            'text' => 'Securities',
        ],
//        'maturity_profile' => [
//            'text' => 'Maturity profile',
//        ],
        'issuance' => [
            'text' => 'Issuance',
        ],
    ];

    public function __construct(
        $entity,
        $currentPage,
        $options = []
    ) {
        parent::__construct($entity, $options);
        $this->currentPage = $currentPage;
    }

    public function getItems(): array
    {
        $items = [];
        foreach ($this->tabs as $tab => $data) {
            $items[] = new EntityNavItemPresenter(
                $this->getRouteName($tab),
                $this->getRouteParams(),
                $data['text'],
                ($this->currentPage == $tab)
            );
        }
        return $items;
    }

    private function getRoutePrefix(): string
    {
        if ($this->domainModel) {
            return $this->domainModel->getRoutePrefix() . '_';
        }
        return 'overview_';
    }

    private function getRouteName($suffix): string
    {
        return $this->getRoutePrefix() . $suffix;
    }

    private function getRouteParams(): array
    {
        if ($this->domainModel) {
            return [$this->getRoutePrefix() . 'id' => $this->domainModel->getId()];
        }
        return [];
    }
}
