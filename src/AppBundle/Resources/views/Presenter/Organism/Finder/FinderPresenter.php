<?php

namespace AppBundle\Presenter\Organism\Finder;

use AppBundle\Presenter\Presenter;

class FinderPresenter extends Presenter
{
    private $items;
    private $initial;
    private $routeSuffix;

    public function __construct(
        array $items,
        bool $initial = true,
        string $routeSuffix = 'show'
    ) {
        parent::__construct(null, []);
        $this->items = $items;
        $this->initial = $initial;
        $this->routeSuffix = $routeSuffix;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getAllClass(): string
    {
        return ($this->initial) ? 'finder__active' : '';
    }

    public function getAllPath():string
    {
        return 'overview' . $this->routeSuffix;
    }
}
