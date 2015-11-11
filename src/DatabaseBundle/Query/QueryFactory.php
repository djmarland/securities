<?php

namespace DatabaseBundle\Query;

use DatabaseBundle\Mapper\MapperFactory;
use Doctrine\ORM\EntityManager;

class QueryFactory
{
    protected $entityManager;

    protected $mapperFactory;

    public function __construct(
        EntityManager $entityManager,
        MapperFactory $mapperFactory
    ) {
        $this->entityManager = $entityManager;
        $this->mapperFactory = $mapperFactory;
    }

    public function createSettingsQuery()
    {
        $query = new SettingsQuery(
            $this->entityManager,
            $this->mapperFactory
        );
        return $query;
    }

    public function createUsersQuery()
    {
        $query = new UsersQuery(
            $this->entityManager,
            $this->mapperFactory
        );
        return $query;
    }
}
