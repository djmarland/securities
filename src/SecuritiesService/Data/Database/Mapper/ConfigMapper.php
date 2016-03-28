<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Config;
use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\ValueObject\UUID;

class ConfigMapper extends Mapper
{
    public function getDomainModel(array $item): Entity
    {
        $id = new UUID($item['id']);
        $domainEntity = new Config(
            $id,
            $item['siteTitle'],
            $item['siteTagline'],
            $item['features']
        );
        return $domainEntity;
    }
}
