<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\Entity\Enum\Features;
use SecuritiesService\Domain\ValueObject\UUID;
use DateTime;

class Config extends Entity
{
    private $siteTitle;
    private $siteHostName;
    private $siteTagLine;
    private $adsInDevMode;
    private $features;

    public function __construct(
        UUID $id,
        string $siteTitle,
        string $siteHostName,
        string $siteTagLine,
        bool $adsInDevMode,
        array $features
    ) {
        parent::__construct($id);

        $this->siteTitle = $siteTitle;
        $this->siteHostName = $siteHostName;
        $this->siteTagLine = $siteTagLine;
        $this->adsInDevMode = $adsInDevMode;
        $this->features = $features;
    }

    public function getSiteTitle(): string
    {
        return $this->siteTitle;
    }

    public function getSiteHostName(): string
    {
        return $this->siteHostName;
    }

    public function getSiteTagLine(): string
    {
        return $this->siteTagLine;
    }

    public function adsInDevMode(): bool
    {
        return $this->adsInDevMode;
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
            $features[$feature] = $this->featureIsActive(Features::$feature());
        }
        return $features;
    }

    public function featureIsActive(
        Features $feature
    ): bool {
        $featureName = $feature->getKey();
        return (
            isset($this->features[$featureName]) &&
            $this->features[$featureName]
        );
    }
}
