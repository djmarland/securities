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

    public function createSecuritiesQuery(): SecuritiesQuery
    {
        $query = new SecuritiesQuery(
            $this->entityManager,
            $this->mapperFactory
        );
        return $query;
    }
}
