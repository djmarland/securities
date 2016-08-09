<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\LSEAnnouncement;
use SecuritiesService\Domain\ValueObject\UUID;

class LSEAnnouncementMapper extends Mapper
{
    public function getDomainModel(array $item): Entity
    {
        $id = new UUID($item['id']);
        $entity = new LSEAnnouncement(
            $id,
            $item['title'],
            $item['description'],
            $item['link'],
            \DateTimeImmutable::createFromMutable($item['dateFetched']),
            $item['status']
        );
        return $entity;
    }
}
