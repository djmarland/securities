<?php
namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;

/**
 * Interface MapperInterface
 */
interface MapperInterface
{
    public function getDomainModel(array $item): Entity;
}
