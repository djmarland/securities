<?php

namespace AppBundle\Presenter\Organism\Group;

use SecuritiesService\Domain\Entity\ParentGroup;
use AppBundle\Presenter\Presenter;

class GroupPresenter extends Presenter implements GroupPresenterInterface
{

    private $group;

    private $companies;

    public function __construct(
        ParentGroup $group,
        array $companies,
        array $options = []
    ) {
        parent::__construct(null, $options);

        $this->group = $group;
        $this->companies = $companies;
    }

    public function getName():string
    {
        return $this->group->getName();
    }

    public function getCompanies():array
    {
        return $this->companies;
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
