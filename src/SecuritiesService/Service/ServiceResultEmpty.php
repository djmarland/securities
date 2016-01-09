<?php

namespace SecuritiesService\Service;

class ServiceResultEmpty extends ServiceResult
{
    public function __construct()
    {
        // empty
    }


    public function hasResult(): bool
    {
        return false;
    }

    public function getTotal():int {
        return 0;
    }

    public function getDomainModels(): array
    {
        return [];
    }
}