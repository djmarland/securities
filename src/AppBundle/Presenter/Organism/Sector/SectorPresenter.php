<?php

namespace AppBundle\Presenter\Organism\Sector;

use SecuritiesService\Domain\Entity\Sector;
use AppBundle\Presenter\Presenter;

class SectorPresenter extends Presenter implements SectorPresenterInterface
{

    private $sector;

    private $groups;

    public function __construct(
        Sector $sector,
        array $groups,
        array $options = []
    ) {
        parent::__construct(null, $options);

        $this->sector = $sector;
        $this->groups = $groups;
    }

    public function getName():string
    {
        return $this->sector->getName();
    }

    public function getGroups():array
    {
        return $this->groups;
    }

    public function getID():string
    {
        return (string) $this->sector->getId();
    }

    public function getLetter():string
    {
        return substr($this->getName(), 0, 1);
    }
}
