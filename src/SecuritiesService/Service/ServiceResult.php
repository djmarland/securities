<?php

namespace SecuritiesService\Service;

class ServiceResult implements ServiceResultInterface
{
    private $total;

    private $domainModels = [];

    public function __construct(
        array $domainModels,
        int $total = null
    ) {
        $this->domainModels = $domainModels;
        if (!is_null($total)) {
            $this->total = (int)$total;
        }
    }

    public function hasResult(): bool
    {
        return !empty($this->domainModels);
    }

    public function setTotal(int $total)
    {
        $this->total = $total;
    }

    public function setDomainModels(array $models)
    {
        $this->domainModels = $models;
    }

    public function getTotal(): int
    {
        if (is_null($this->total)) {
            // @todo - throw a better exception
            throw new \Exception('Tried to call total when no count had been asked for');
        }
        return $this->total;
    }

    public function getDomainModels(): array
    {
        return $this->domainModels;
    }

    public function getDomainModel()
    {
        $models = $this->getDomainModels();
        if (!empty($models)) {
            return reset($models);
        }
        return null;
    }
}
