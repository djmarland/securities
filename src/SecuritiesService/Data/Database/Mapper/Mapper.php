<?php

namespace SecuritiesService\Data\Database\Mapper;

abstract class Mapper implements MapperInterface
{
    protected $mapperFactory;

    public function __construct(
        MapperFactory $mapperFactory
    ) {
        $this->mapperFactory = $mapperFactory;
    }
}
