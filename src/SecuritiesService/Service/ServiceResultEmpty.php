<?php

namespace SecuritiesService\Service;

class ServiceResultEmpty extends ServiceResult
{
    public function __construct()
    {
        // empty
    }


    public function getTotal() {
        return 0;
    }

    public function getDomainModels(): array
    {
        return array();
    }
}