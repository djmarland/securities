<?php

namespace SecuritiesService\Service\Filter;

use Doctrine\ORM\QueryBuilder;
use SecuritiesService\Domain\Entity\Currency;
use SecuritiesService\Domain\Entity\Product;
use SecuritiesService\Domain\ValueObject\Bucket;
use SecuritiesService\Domain\ValueObject\BucketUndated;

class SecuritiesFilter
{
    private $entities;

    /**
     * Pump entities into here to filter for securities that match
     * those properties
     */
    public function __construct(...$entities)
    {
        // filter null entities
        $this->entities = array_filter($entities);
    }

    public function isActive()
    {
        return !empty($this->entities);
    }

    public function getStatus()
    {
        $statuses = [];
        foreach ($this->entities as $entity) {
            if ($entity instanceof Product) {
                $statuses[] = $entity->getName();
            } elseif ($entity instanceof Currency) {
                $statuses[] = $entity->getCode();
            } elseif ($entity instanceof Bucket) {
                $statuses[] = 'Due to mature ' . $entity->getName();
            } elseif (isset($entity['start'])) {
                $statuses[] = 'Issued ' . $entity['start']->format('d M Y') .
                    ' - ' . $entity['displayEnd']->format('d M Y');
            }
        }
        return implode(', ', $statuses);
    }

    public function apply(
        QueryBuilder $qb,
        string $tbl
    ): QueryBuilder {

        foreach ($this->entities as $entity) {
            if ($entity instanceof Product) {
                $qb = $this->applyProduct($qb, $tbl, $entity);
            } elseif ($entity instanceof Currency) {
                $qb = $this->applyCurrency($qb, $tbl, $entity);
            } elseif ($entity instanceof Bucket) {
                $qb = $this->applyBucket($qb, $tbl, $entity);
            } elseif (isset($entity['start'])) {
                $qb = $this->applyDateRange($qb, $tbl, $entity);
            }
        }
        return $qb;
    }

    private function applyProduct(
        QueryBuilder $qb,
        string $tbl,
        Product $product
    ) {
        return $qb->andWhere('IDENTITY(' . $tbl . '.product) = :product_id')
            ->setParameter('product_id', $product->getId()->getBinary());
    }

    private function applyCurrency(
        QueryBuilder $qb,
        string $tbl,
        Currency $currency
    ) {
        return $qb->andWhere('IDENTITY(' . $tbl . '.currency) = :currency_id')
            ->setParameter('currency_id', $currency->getId()->getBinary());
    }

    private function applyBucket(
        QueryBuilder $qb,
        string $tbl,
        Bucket $bucket
    ) {
        if ($bucket instanceof BucketUndated) {
            return $qb->andWhere($tbl . '.maturityDate is NULL');
        }

        // todo - convert to DateTimeNotImmutable?!
        $qb->andWhere($tbl . '.maturityDate >= :maturityDateLower')
            ->setParameter('maturityDateLower', $bucket->getStartDate());

        if ($bucket->getEndDate()) {
            $qb->andWhere($tbl . '.maturityDate < :maturityDateUpper')
                ->setParameter('maturityDateUpper', $bucket->getEndDate());
        }
        return $qb;
    }

    private function applyDateRange(
        QueryBuilder $qb,
        string $tbl,
        array $range
    ) {
        return $qb->andWhere($tbl . '.startDate >= :startDateLower')
            ->andWhere($tbl . '.startDate < :startDateUpper')
            ->setParameter('startDateLower', $range['start'])
            ->setParameter('startDateUpper', $range['end']);
    }
}
