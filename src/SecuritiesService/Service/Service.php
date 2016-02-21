<?php

namespace SecuritiesService\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use SecuritiesService\Data\Database\Mapper\MapperFactory;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\ValueObject\UUID;

abstract class Service
{
    const TBL = 'tbl';

    const DEFAULT_LIMIT = 50;
    const DEFAULT_PAGE = 1;

    protected $entityManager;
    protected $mapperFactory;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->mapperFactory = new MapperFactory();
    }

    protected function getEntity(string $name): EntityRepository
    {
        return $this->entityManager
            ->getRepository('SecuritiesService:' . $name);
    }

    protected function getQueryBuilder(string $name): QueryBuilder
    {
        $entity = $this->getEntity($name);
        return $entity->createQueryBuilder(self::TBL);
    }

    protected function getDomainFromQuery(
        QueryBuilder $query,
        string $entityType
    ): array {
        $result = $query->getQuery()->getArrayResult();
        $entities = [];
        $mapper = $this->mapperFactory->createMapper($entityType);
        foreach ($result as $item) {
            $entities[] = $mapper->getDomainModel($item);
        }
        return $entities;
    }

    protected function simpleFindByUUID(
        UUID $id,
        string $type
    ) {
        $qb = $this->getQueryBuilder($type);
        $qb->select(self::TBL)
            ->where(self::TBL . '.id = :id')
            ->setParameters([
                'id' => $id->getBinary(),
            ]);

        $results = $this->getDomainFromQuery($qb, $type);
        if (empty($results)) {
            throw new EntityNotFoundException('No such item with ID ' . $id);
        }

        return reset($results);
    }

    protected function getOffset(
        int $limit,
        int $page
    ): int {
        return ($limit * ($page - 1));
    }

    protected function paginate(
        QueryBuilder $qb,
        int $limit,
        int $page
    ) {
        return $qb->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));
    }
}
