<?php

namespace AppBundle\Service;

interface ServiceResultInterface
{
    public function setTotal($total);
    public function setDomainModels(array $models);

    public function getTotal();
    public function getDomainModels();
    public function getDomainModel();
}
