<?php
namespace DatabaseBundle\Mapper;

/**
 * Interface MapperInterface
 */
interface MapperInterface
{
    public function getDomainModel($dataObject);
    public function getOrmEntity($dataObject);
}
