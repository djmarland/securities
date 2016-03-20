<?php

namespace AppBundle\Presenter\Organism\EntityContext;

interface EntityContextPresenterInterface
{
    public function isVisible(): bool;
    public function hasParents(): bool;
    public function getParents(): array;
    public function getEntityName(): string;
}
