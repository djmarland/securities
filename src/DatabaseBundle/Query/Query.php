<?php

namespace DatabaseBundle\Query;

use DatabaseBundle\Entity\Entity;
use DatabaseBundle\Mapper\MapperFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

abstract class Query {

    protected $entityManager;

    protected $mapperFactory;

    public function __construct(
        EntityManager $entityManager,
        MapperFactory $mapperFactory
    ) {
        $this->entityManager = $entityManager;
        $this->mapperFactory = $mapperFactory;
    }

    public function getEntity(string $name): EntityRepository
    {
        return $this->entityManager
            ->getRepository('DatabaseBundle:' . $name);
    }

    protected function getFromEntity(EntityRepository  $entity): array
    {
        $result = $entity->findBy(
            $this->by,
            $this->sort,
            $this->limit,
            $this->offset
        );
        return $this->getDomainModels($result);
    }

    protected function countFromEntity(EntityRepository  $entity): int
    {
        $qb = $entity->createQueryBuilder('tbl');
        $qb->select('count(tbl.id)');

        $i = 1;
        $params = [];
        if (!empty($this->by)) {
            foreach ($this->by as $key => $by) {
                $qb->andWhere('tbl.' . $key . ' = ?' . $i);
                $params[$i] = $by;
                $i++;
            }
            $qb->setParameters($params);
        }
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    protected $by = [];

    protected $sort = ['id' => 'ASC'];

    protected $limit = null;

    protected $offset = null;

    public function paginate(
        int $perPage,
        int $page
    ): Query {
        $this->limit = $perPage;
        $this->offset = ($perPage * ($page - 1));
        return $this;
    }

    public function byId(int $id): Query
    {
        $this->by['id'] = $id;
        return $this;
    }

    public function sortByCreationDate(string $direction = 'DESC'): Query
    {
        $this->sort = ['created_at' => $direction];
        return $this;
    }

    public function getDomainModels($items): array
    {
        if (!$items) {
            return null;
        }
        if (!is_array($items)) {
            $items = [$items];
        }

        $domainModels = array();
        foreach ($items as $item) {
            $mapper = $this->mapperFactory->getMapper($item);
            $domainModels[] = $mapper->getDomainModel($item);
        }
        return $domainModels;
    }
}