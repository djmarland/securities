<?php

namespace SecuritiesService\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use SecuritiesService\Data\Database\Mapper\MapperFactory;

abstract class Service
{
    const TBL = 'tbl';

    const DEFAULT_LIMIT = 50;
    const DEFAULT_PAGE = 1;

    protected $entityManager;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    protected function getEntity(string $name): EntityRepository
    {
        return $this->entityManager
            ->getRepository('SecuritiesService:' . $name);
    }

    protected function getQueryBuilder(string $name) {
        $entity = $this->getEntity($name);
        return $entity->createQueryBuilder(self::TBL);
    }

    protected function getDomainModel($item, $type)
    {
        // @todo - potential bug here if we ever pass an array of results into this method
        $models = $this->getDomainModels([$item], $type);
        if ($models) {
            return reset($models);
        }
        return null;
    }

    protected function getDomainModels($items, $type)
    {
        if (!$items) {
            return null;
        }
        $items = ensure_array($items);

        $mapperFactory = new MapperFactory();
        $domainModels = array();
        $mapper = $mapperFactory->createMapper($type);
        foreach ($items as $item) {
            $domainModels[] = $mapper->getDomainModel($item);
        }
        return $domainModels;
    }

    protected function getOffset(
        int $limit,
        int $page
    ): int {
        return ($limit * ($page - 1));
    }

    protected function getServiceResult(QueryBuilder $qb, $type)
    {
        $result = $qb->getQuery()->getArrayResult();
        $data = $this->getDomainModels($result, $type);

        if ($data) {
            return new ServiceResult($data);
        }
        return new ServiceResultEmpty();
    }
}
