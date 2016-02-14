<?php

namespace AppBundle\Presenter\Organism\Finder;

use AppBundle\Presenter\Presenter;

class FinderItemPresenter extends Presenter
{
    private $url;
    private $title;
    private $items;

    public function __construct(
        string $url,
        string $title,
        array $items = null
    )
    {
        parent::__construct(null, []);

        $this->url = $url;
        $this->title = $title;
        $this->items = $items;
    }

    public function getTitle():string
    {
        return $this->title;
    }

    public function getUrl():string
    {
        return $this->url;
    }

    public function getItems()
    {
        if (!empty($this->items)) {
            return $this->items;
        }
        return null;
    }
}