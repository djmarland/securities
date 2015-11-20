<?php

namespace AppBundle\Service;

use DatabaseBundle\Mapper\MapperFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

abstract class Service
{
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

        $domainModels = array();
        foreach ($items as $item) {
            $mapper = $this->mapperFactory->getMapper($item);
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
