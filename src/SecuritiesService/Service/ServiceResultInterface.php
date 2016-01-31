<?php

namespace SecuritiesService\Service;

interface ServiceResultInterface
{
    public function setTotal(int $total);
    public function setDomainModels(array $models);

    public function hasResult(): bool;

    public function getTotal(): int;
    public function getDomainModels(): array;
    public function getDomainModel();
    public function getResultCount(): int;
}
