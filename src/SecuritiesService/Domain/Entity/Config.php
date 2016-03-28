<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\Entity\Enum\Features;
use SecuritiesService\Domain\ValueObject\UUID;
use DateTime;

class Config extends Entity
{
    private $siteTitle;
    private $siteTagline;
    private $features;

    public function __construct(
        UUID $id,
        string $siteTitle,
        string $siteTagline,
        array $features
    ) {
        parent::__construct($id);

        $this->siteTitle = $siteTitle;
        $this->siteTagline = $siteTagline;
        $this->features = $features;
    }

    public function getSiteTitle(): string
    {
        return $this->siteTitle;
    }

    public function getSiteTagline(): string
    {
        return $this->siteTagline;
    }

    public function getFeatures(): array
    {
        return $this->features;
    }

    public function getFeatureList(): array
    {
        $allFeatures = Features::keys();
        $features = [];
        foreach ($allFeatures as $feature) {
            $features[$feature] = $this->featureIsActive($feature);
        }
        return $features;
    }

    public function featureIsActive(
        string $featureName
    ): bool {
        return (
            isset($this->features[$featureName]) &&
            $this->features[$featureName]
        );
    }
}
