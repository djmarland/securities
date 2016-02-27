<?php

namespace AppBundle\Presenter\Organism\Adverts;

interface AdvertsPresenterInterface
{
    public function areActive(): bool;
    public function getVariantVars(string $variant = null): array;
    public function getClientId(): string;
    public function getSlotId(): string;
    public function getVariant(): string;
}
