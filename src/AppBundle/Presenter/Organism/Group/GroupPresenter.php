<?php

namespace AppBundle\Presenter\Organism\Group;

use SecuritiesService\Domain\Entity\ParentGroup;
use AppBundle\Presenter\Presenter;

class GroupPresenter extends Presenter implements GroupPresenterInterface
{

    private $group;

    public function __construct(
        ParentGroup $group,
        array $options = [
        ]
    )
    {
        parent::__construct(null, $options);

        $this->group = $group;
    }

    public function getName():string
    {
        return ucwords(strtolower($this->group->getName()));
    }

    public function getID():string
    {
        return (string) $this->group->getId();
    }

    public function getLetter():string
    {
        return substr($this->getName(), 0, 1);
    }
}