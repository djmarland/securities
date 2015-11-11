<?php

namespace AppBundle\Service;

use DatabaseBundle\Query\QueryFactory;

abstract class Service
{
    /**
     * @param QueryFactory $queryFactory
     */
    public function __construct(
        QueryFactory $queryFactory
    ) {
        $this->setQueryFactory($queryFactory);
    }

    /**
     * @var QueryFactory
     */
    public $queryFactory;

    /**
     * @return QueryFactory
     */
    protected function getQueryFactory()
    {
        return $this->queryFactory;
    }

    /**
     * @param QueryFactory $queryFactory
     */
    protected function setQueryFactory(QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
    }
}
