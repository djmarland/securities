<?php

namespace AppBundle\Presenter\Organism\Finder;

use AppBundle\Presenter\Presenter;

class FinderPresenter extends Presenter
{
    private $items;

    public function __construct(
        array $items
    )
    {
        parent::__construct(null, []);
        $this->items = $items;
    }

    public function getItems():array
    {
        return $this->items;
    }
}