<?php

namespace SecuritiesService\Service;

use SecuritiesService\Data\Database\Entity\Config as DbConfig;
use SecuritiesService\Domain\Entity\Config;
use SecuritiesService\Domain\Entity\Enum\Features;
use SecuritiesService\Domain\Exception\EntityNotFoundException;

class ConfigService extends Service
{
    const SERVICE_ENTITY = 'Config';

    public function get(): Config
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL);

        $results = $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
        if (empty($results)) {
            throw new EntityNotFoundException('No Config found');
        }

        return reset($results);
    }

    public function setSettings(
        array $newSettings
    ) {
        $entity = $this->getDbEntity();
        $settings = $entity->settings;
        $settings = array_merge($settings, $newSettings);
        $entity->settings = $settings;
        return $this->save($entity);
    }

    public function setActiveFeatures(
        array $features
    ) {
        $allFeatures = Features::keys();
        $activeFeatures = [];
        foreach ($allFeatures as $feature) {
            $activeFeatures[$feature] = (in_array($feature, $features));
        }
        $entity = $this->getDbEntity();
        $entity->features = $activeFeatures;
        return $this->save($entity);
    }

    private function getDbEntity(): DbConfig
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL);
        $result = $qb->getQuery()->getSingleResult();
        if (!$result) {
            throw new EntityNotFoundException('No Config found');
        }
        return $result;
    }

    private function save(DbConfig $entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return;
    }
}
