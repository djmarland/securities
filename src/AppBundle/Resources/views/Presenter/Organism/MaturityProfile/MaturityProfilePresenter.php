<?php

namespace AppBundle\Presenter\Organism\MaturityProfile;

use AppBundle\Presenter\Molecule\Money\MoneyPresenter;
use AppBundle\Presenter\Presenter;
use SecuritiesService\Domain\Entity\Entity;

class MaturityProfilePresenter extends Presenter implements MaturityProfilePresenterInterface
{
    protected $results;

    protected $options = [];

    public function __construct(
        Entity $entity = null,
        array $results = [],
        array $options = []
    ) {
        parent::__construct($entity, $options);
        $this->results = $results;
    }

    public function getBucketTitles()
    {
        $buckets = array_map(function($result) {
            return $result->bucket;
        }, $this->results);
        return array_map(function($bucket) {
            return $bucket->getName();
        }, $buckets);
    }

    public function getHeadings()
    {
        $headings = $this->getBucketTitles();
        $headings[] = 'Total';
    }

    public function getRows()
    {
        return [];
    }
}