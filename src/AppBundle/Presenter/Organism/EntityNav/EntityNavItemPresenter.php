<?php

namespace AppBundle\Presenter\Organism\EntityNav;

class EntityNavItemPresenter
{
    private $routeName;
    private $routeParams;
    private $text;
    private $active = false;

    public function __construct(
        string $routeName,
        array $routeParams,
        string $text,
        bool $active = false
    ) {
        $this->routeName = $routeName;
        $this->routeParams = $routeParams;
        $this->text = $text;
        $this->active = $active;
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
