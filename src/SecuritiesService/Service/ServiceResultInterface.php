<?php

namespace SecuritiesService\Service;

interface ServiceResultInterface
{
    public function setTotal(int $total);
    public function setDomainModels(array $models);

    public function hasResult();

    public function getTotal();
    public function getDomainModels();
    public function getDomainModel();
}
