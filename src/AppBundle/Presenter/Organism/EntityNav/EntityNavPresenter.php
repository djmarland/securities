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

    public function getItems()
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

    private function getRouteName($suffix)
    {
        return $this->getTypeNameFromEntity() . '_' . $suffix;
    }

    private function getRouteParams()
    {
        return [$this->getTypeNameFromEntity() . '_id' => $this->domainModel->getId()];
    }

    private function getTypeNameFromEntity()
    {
        // @todo - abstract this - somewhere
        if ($this->domainModel instanceof Company) {
            return 'issuer';
        }
        if ($this->domainModel instanceof ParentGroup) {
            return 'group';
        }
        if ($this->domainModel instanceof Sector) {
            return 'sector';
        }
        if ($this->domainModel instanceof Industry) {
            return 'industry';
        }
    }
}
