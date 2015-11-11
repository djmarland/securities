<?php

namespace AppBundle\Service;

class ServiceResult implements ServiceResultInterface
{
    /**
     * @var
     */
    private $total;

    /**
     * @var
     */
    private $domainModels = [];

    /**
     * @param $domainModels
     * @param null $total
     */
    public function __construct($domainModels, $total = null)
    {
        if (!is_array($domainModels)) {
            $domainModels = [$domainModels];
        }
        $this->domainModels = $domainModels;
        if (!is_null($total)) {
            $this->total = (int)$total;
        }
    }

    /**
     * @param $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @param $models
     */
    public function setDomainModels(array $models)
    {
        $this->domainModels = $models;
    }

    /**
     * return int
     */
    public function getTotal()
    {
        if (is_null($this->total)) {
            // @todo - throw a better exception
            throw new \Exception('Tried to call total when no count had been asked for');
        }
        return $this->total;
    }

    /**
     * @return mixed
     */
    public function getDomainModels()
    {
        return $this->domainModels;
    }

    /**
     * Get a single domain model (always the first)
     * @return mixed|null
     */
    public function getDomainModel()
    {
        $models = $this->getDomainModels();
        if (!empty($models)) {
            return reset($models);
        }
        return null;
    }
}
