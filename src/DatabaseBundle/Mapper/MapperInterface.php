<?php
namespace DatabaseBundle\Mapper;
use AppBundle\Domain\Entity\Entity;
use DatabaseBundle\Entity\Entity as EntityOrm;

/**
 * Interface MapperInterface
 */
interface MapperInterface
{
    public function getDomainModel(EntityOrm $item): Entity;
}
