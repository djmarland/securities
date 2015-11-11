<?php

namespace DatabaseBundle\Mapper;

use AppBundle\Domain\Entity\Settings;
use AppBundle\Domain\ValueObject\ID;

class SettingsMapper extends Mapper
{
    public function getDomainModel($item)
    {
        $id = new ID($item->getId());
        $settings = new Settings(
            $id,
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
            $item->getActiveStatus(),
            $item->getApplicationName()
        );

        $settings->setOrmEntity($item);
        return $settings;
    }

    public function getOrmEntity($domain)
    {
        $entity = $domain->getOrmEntity();
        // @todo
        if (!$entity) {
            // create a new one
        }
        $entity->setApplicationName($domain->getApplicationName());
        return $entity;
    }
}
