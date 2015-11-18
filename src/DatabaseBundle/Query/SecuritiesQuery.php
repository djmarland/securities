<?php
namespace DatabaseBundle\Query;

class SecuritiesQuery extends Query
{
    const ENTITY_NAME = 'Security';

    public function get()
    {
        $entity = $this->getEntity(self::ENTITY_NAME);
        return $this->getFromEntity($entity);
    }

    public function count(): int
    {
        $entity = $this->getEntity(self::ENTITY_NAME);
        return $this->countFromEntity($entity);
    }

    public function search(string $query)
    {
        $entity = $this->getEntity(self::ENTITY_NAME);
        $qb = $entity->createQueryBuilder('tbl');
        $qb->select();

        $qb->andWhere('tbl.isin LIKE ?0');
        $qb->setParameters(['%' . $query . '%']);

        $qb->setMaxResults($this->limit)
            ->setFirstResult($this->offset);

        $result = $qb->getQuery()->getResult();

        return $this->getDomainModels($result);
    }

    public function countSearch(string $query)
    {
        $entity = $this->getEntity(self::ENTITY_NAME);
        $qb = $entity->createQueryBuilder('tbl');
        $qb->select('count(tbl.id)');

        $qb->andWhere('tbl.isin LIKE ?0');
        $qb->setParameters(['%' . $query . '%']);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function byIsin(string $isin): Query
    {
        $this->by['isin'] = $isin;
        return $this;
    }

    public function sortByIsin(string $direction = 'DESC'): Query
    {
        $this->sort = ['isin' => $direction];
        return $this;
    }
}
