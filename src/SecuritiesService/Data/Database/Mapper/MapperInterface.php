<?php
namespace SecuritiesService\Data\Database\Mapper;
use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Data\Database\Entity\Entity as EntityOrm;

/**
 * Interface MapperInterface
 */
interface MapperInterface
{
    public function getDomainModel(array $item): Entity;
}
