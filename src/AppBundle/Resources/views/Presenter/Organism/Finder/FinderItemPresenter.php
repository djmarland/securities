<?php

namespace AppBundle\Presenter\Organism\Finder;

use AppBundle\Presenter\Presenter;

class FinderItemPresenter extends Presenter
{
    private $url;
    private $title;
    private $active;
    private $items;
    private $listName;

    public function __construct(
        string $url,
        string $title,
        bool $active,
        string $listName = null,
        array $items = null
    ) {
        parent::__construct(null, []);

        $this->url = $url;
        $this->title = $title;
        $this->active = $active;
        $this->items = $items;
        $this->listName = $listName;
    }

    public function getTitle():string
    {
        return $this->title;
    }

    public function getUrl():string
    {
        return $this->url;
    }

    public function isActive():bool
    {
        return $this->active;
    }

    public function getItems()
    {
        if (!empty($this->items)) {
            return $this->items;
        }
        return null;
    }

    public function getHeading(): string
    {
        return $this->listName;
    }

    public function getClass(): string
    {
        return ($this->isActive()) ? 'finder__active' : '';
    }

    public function getIndicatorClass(): string
    {
        return (!!$this->listName) ? '' : 'finder__indicator--nodrop';
    }

    public function getIcon(): string
    {
        if (!!$this->listName) {
            return <<<SVG
<svg viewBox="0 0 24 24">
    <use xlink:href="#icon-arrow"></use>
</svg>
SVG;

        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
    <circle cx="12" cy="12" r="8" />
</svg>
SVG;
    }
}
