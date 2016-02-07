<?php

namespace SecuritiesService\Service;

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
     * @param array $entities
     */
    public function __construct(...$entities) {
        $this->entities = array_filter($entities);
    }

    public function apply(
        QueryBuilder $qb,
        string $tbl
    ): QueryBuilder {

        foreach ($this->entities as $entity) {
            if ($entity instanceof Product) {
                $qb->andWhere('IDENTITY(' . $tbl . '.product) = :product_id')
                    ->setParameter('product_id', $entity->getId());
            }
            if ($entity instanceof Currency) {
                $qb->andWhere('IDENTITY(' . $tbl . '.currency) = :currency_id')
                    ->setParameter('currency_id', $entity->getId());
            }

            if ($entity instanceof Bucket) {
                if ($entity instanceof BucketUndated) {
                    $qb->andWhere($tbl . '.maturity_date is NULL');
                } else {
                    $qb->andWhere($tbl . '.maturity_date > :maturity_date_lower')
                        ->andWhere($tbl . '.maturity_date <= :maturity_date_upper')
                        ->setParameter('maturity_date_lower', $entity->getStartDate())
                        ->setParameter('maturity_date_upper', $entity->getEndDate());
                }
            }
        }

        return $qb;
    }
}