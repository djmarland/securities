<?php

namespace AppBundle\Presenter\Organism\Finder;

use AppBundle\Presenter\Presenter;

class FinderPresenter extends Presenter
{
    private $items;
    private $initial;

    public function __construct(
        array $items,
        bool $initial = true
    ) {
        parent::__construct(null, []);
        $this->items = $items;
        $this->initial = $initial;
    }

    public function getItems():array
    {
        return $this->items;
    }

    public function getAllClass():string
    {
        return ($this->initial) ? 'finder__active' : '';
    }
}
