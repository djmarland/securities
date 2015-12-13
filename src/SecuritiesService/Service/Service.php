<?php

namespace SecuritiesService\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use SecuritiesService\Data\Database\Mapper\MapperFactory;

abstract class Service
{
    protected $entityManager;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function getEntity(string $name): EntityRepository
    {
        return $this->entityManager
            ->getRepository('SecuritiesService:' . $name);
    }

    public function getQueryBuilder(string $name) {
        $entity = $this->getEntity($name);
        return $entity->createQueryBuilder('tbl');
    }

    public function getDomainModels($items)
    {
        if (!$items) {
            return null;
        }
        if (!is_array($items)) {
            $items = [$items];
        }

        $mapperFactory = new MapperFactory();
        $domainModels = array();
        foreach ($items as $item) {
            $mapper = $mapperFactory->getMapper($item);
            $domainModels[] = $mapper->getDomainModel($item);
        }
        return $domainModels;
    }

    public function getOffset(
        int $limit,
        int $page
    ): int {
        return ($limit * ($page - 1));
    }

    public function getServiceResult($result)
    {
        $data = $this->getDomainModels($result);

        if ($data) {
            return new ServiceResult($data);
        }
        return new ServiceResultEmpty();
    }
}
