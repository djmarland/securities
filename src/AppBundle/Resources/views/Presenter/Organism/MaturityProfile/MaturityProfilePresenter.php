<?php

namespace AppBundle\Presenter\Organism\MaturityProfile;

use AppBundle\Presenter\Molecule\Money\MoneyPresenter;
use AppBundle\Presenter\Presenter;
use SecuritiesService\Domain\Entity\Entity;

class MaturityProfilePresenter extends Presenter implements MaturityProfilePresenterInterface
{
    protected $results;
    protected $resultsByProduct;
    protected $allBuckets;

    protected $options = [];

    public function __construct(
        Entity $entity = null,
        array $results = [],
        array $allBuckets = [],
        array $options = []
    ) {
        parent::__construct($entity, $options);
        $this->results = $results;
        $this->allBuckets = $allBuckets;
    }

    public function getBucketTitles(): array
    {
        $buckets = array_map(function($result) {
            return $result->bucket;
        }, $this->results);
        return array_map(function($bucket) {
            return $bucket->getName();
        }, $buckets);
    }

    public function getHeadings(): array
    {;
        $headings = ['Product'];
        $headings = array_merge($headings, $this->getBucketTitles());
//        $headings[] = 'Total';
        return $headings;
    }

    public function getRows(): array
    {
        $results = $this->getResultsByProduct();
        $rows = [];

        foreach ($results as $result) {
            $row = [];
            $row[] = [
                'element' => 'th',
                'link' => null,
                'text' => $result->product->getName(),
                'presenter' => null,
            ];
            foreach ($result->buckets as $bucketData) {
                if ($bucketData->total) {
                    $link = [
                        'params' => [
                            'bucket' => $bucketData->bucket->getKey(),
                            'product' => $result->product->getNumber()
                        ],
                    ];

                    if ($this->domainModel) {
                        $link['path'] = $this->domainModel->getRoutePrefix() . '_securities';
                        $link['params'][$this->domainModel->getRoutePrefix() . '_id'] =
                            (string) $this->domainModel->getId();
                    } else {
                        $link['path'] = 'overview_securities';
                    }

                    $col['link'] = $link;


                    $row[] = [
                        'element' => 'td',
                        'link' => $link,
                        'text' => null,
                        'presenter' => new MoneyPresenter($bucketData->total, ['scale' => true]),
                    ];
                } else {
                    $row[] = [
                        'element' => 'td',
                        'link' => null,
                        'text' => '-',
                        'presenter' => null,
                    ];
                }
            }
//            Disabled Total column
//            $row[] = [
//                'element' => 'td',
//                'link' => null,
//                'text' => null,
//                'presenter' => new MoneyPresenter($result->productTotal, ['scale' => true]),
//            ];
            $rows[] = $row;
        }

        return $rows;
    }

    private function getResultsByProduct(): array
    {
        if (!$this->resultsByProduct) {
            $map = $this->mapToBucketKeys($this->results);
            $byProduct = [];
            foreach ($this->getProducts($map) as $product) {
                $productTotal = 0;
                $buckets = [];
                foreach ($this->allBuckets as $bucket) {
                    $bucketData = (object) [
                        'bucket' => $bucket,
                        'total' => null
                    ];

                    if (isset($map[$bucket->getKey()])) {
                        $matches = array_filter($map[$bucket->getKey()]->sums, function ($entry) use ($product) {
                            return ($entry->product == $product);
                        });
                        if (!empty($matches)) {
                            $match = reset($matches);
                            $bucketData->total = $match->total;
                            $productTotal += $bucketData->total;
                        }
                    }
                    $buckets[] = $bucketData;
                }
                $byProduct[$product->getNumber()] = (object) [
                    'product' => $product,
                    'buckets' => $buckets,
                    'productTotal' => $productTotal
                ];
            }
            $this->resultsByProduct = array_values($byProduct);
        }
        return $this->resultsByProduct;
    }

    private function getProducts(array $results): array
    {
        $products = [];
        foreach ($results as $resultItem) {
            foreach ($resultItem->sums as $productItem) {
                $products[$productItem->product->getNumber()] = $productItem->product;
            }
        }
        ksort($products);
        return array_reverse($products);
    }

    private function mapToBucketKeys(array $results): array
    {
        $keyed = [];
        foreach ($results as $result) {
            $keyed[$result->bucket->getKey()] = $result;
        }
        return $keyed;
    }
}