<?php

namespace SecuritiesService\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use SecuritiesService\Data\BucketProviderInterface;
use SecuritiesService\Data\Database\Mapper\MapperFactory;
use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\ValueObject\UUID;

abstract class Service
{
    const TBL = 'tbl';

    const DEFAULT_LIMIT = 50;
    const DEFAULT_PAGE = 1;

    protected $entityManager;
    protected $bucketProvider;
    protected $appTimeProvider;
    protected $mapperFactory;

    public function __construct(
        EntityManager $entityManager,
        BucketProviderInterface $bucketProvider,
        DateTimeImmutable $appTimeProvider
    ) {
        $this->entityManager = $entityManager;
        $this->mapperFactory = new MapperFactory();
        $this->bucketProvider = $bucketProvider;
        $this->appTimeProvider = $appTimeProvider;
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
        QueryBuilder $qb,
        string $entityType
    ): array {
        return $this->getDomainFromQueryObject($qb->getQuery(), $entityType);
    }

    protected function getDomainFromQueryObject(
        $query,
        string $entityType
    ): array {
        $result = $query->getArrayResult();
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
    ): Entity {
        $qb = $this->getQueryBuilder($type);
        $qb->select(self::TBL)
            ->where(self::TBL . '.id = :id')
            ->setParameter('id', $id->getBinary());

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
    ): QueryBuilder {
        return $qb->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));
    }
}
