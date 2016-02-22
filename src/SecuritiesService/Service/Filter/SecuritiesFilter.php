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

    public function apply(
        QueryBuilder $qb,
        string $tbl
    ): QueryBuilder {

        foreach ($this->entities as $entity) {
            if ($entity instanceof Product) {
                $qb->andWhere('IDENTITY(' . $tbl . '.product) = :product_id')
                    ->setParameter('product_id', $entity->getId()->getBinary());
            } elseif ($entity instanceof Currency) {
                $qb->andWhere('IDENTITY(' . $tbl . '.currency) = :currency_id')
                    ->setParameter('currency_id', $entity->getId()->getBinary());
            } elseif ($entity instanceof Bucket) {
                if ($entity instanceof BucketUndated) {
                    $qb->andWhere($tbl . '.maturityDate is NULL');
                } else {
                    $qb->andWhere($tbl . '.maturityDate > :maturityDateLower')
                        ->andWhere($tbl . '.maturityDate <= :maturityDateUpper')
                        ->setParameter('maturityDateLower', $entity->getStartDate())
                        ->setParameter('maturityDateUpper', $entity->getEndDate());
                }
            } elseif (isset($entity['start'])) {
                $qb->andWhere($tbl . '.startDate >= :startDateLower')
                    ->andWhere($tbl . '.startDate < :startDateUpper')
                    ->setParameter('startDateLower', $entity['start'])
                    ->setParameter('startDateUpper', $entity['end']);
            }
        }

        return $qb;
    }
}
