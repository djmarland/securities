<?php

namespace SecuritiesService\Service;

use SecuritiesService\Domain\Entity\Company;
use SecuritiesService\Domain\Entity\ParentGroup;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\ValueObject\UUID;

class IssuersService extends Service
{
    const SERVICE_ENTITY = 'Company';

    public function findByUUID(
        UUID $id
    ): Company {
        $groupTbl = 'g';
        $sectorTbl = 's';
        $industryTbl = 'i';

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL, $groupTbl, $sectorTbl, $industryTbl)
            ->where(self::TBL . '.id = :id')
            ->leftJoin(self::TBL . '.parentGroup', $groupTbl)
            ->leftJoin($groupTbl . '.sector', $sectorTbl)
            ->leftJoin($sectorTbl . '.industry', $industryTbl)
            ->setParameter('id', $id->getBinary());

        $results = $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
        if (empty($results)) {
            throw new EntityNotFoundException();
        }

        return reset($results);
    }

    public function findAll(
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL);
        $qb->orderBy(self::TBL . '.name', 'ASC');
        $qb = $this->paginate($qb, $limit, $page);
        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function findAllSimple(): array
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL);
        $qb->orderBy(self::TBL . '.name', 'ASC');
        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function countAll(): int
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('count(' . self::TBL . '.id)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findAllByGroup(
        ParentGroup $group,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL)
            ->where('IDENTITY(' . self::TBL . '.parentGroup) = :parent_group_id')
            ->orderBy(self::TBL . '.name', 'ASC')
            ->setParameter('parent_group_id', $group->getId()->getBinary());

        $qb = $this->paginate($qb, $limit, $page);
        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function findAllInGroups(): array
    {
        $groupTbl = 'g';

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL, $groupTbl)
            ->leftJoin(self::TBL . '.parentGroup', $groupTbl)
            ->addOrderBy($groupTbl . '.name', 'ASC')
            ->addOrderBy(self::TBL . '.name', 'ASC');

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function search(
        string $query,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->where(self::TBL . '.name LIKE :query')
            ->addOrderBy(self::TBL . '.name', 'ASC')
            ->setParameter('query', '%' . $query . '%');

        $qb = $this->paginate($qb, $limit, $page);
        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }
}
