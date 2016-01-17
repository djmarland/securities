<?php

namespace SecuritiesService\Service;

use SecuritiesService\Domain\Entity\ParentGroup;
use SecuritiesService\Domain\ValueObject\ID;

class IssuersService extends Service
{
    const COMPANY_ENTITY = 'Company';

    public function findAndCountAll(
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {

        // count them first (cheaper if zero)
        $count = $this->countAll();
        if (0 == $count) {
            return new ServiceResultEmpty();
        }

        // find the latest
        $result = $this->findAll($limit, $page);
        $result->setTotal($count);
        return $result;
    }

    public function countAll(): int
    {
        $qb = $this->getQueryBuilder(self::COMPANY_ENTITY);
        $qb->select('count(' . self::TBL . '.id)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findAll(
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {
        $qb = $this->getQueryBuilder(self::COMPANY_ENTITY);
        $qb->select(self::TBL);
        $qb->orderBy(self::TBL . '.name', 'ASC');
        $qb->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));
        $result = $qb->getQuery()->getResult();
        return $this->getServiceResult($result);
    }

    public function findAndCountAllByGroup(
        ParentGroup $group,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {

        // count them first (cheaper if zero)
        $count = $this->countAllByGroup($group);
        if (0 == $count) {
            return new ServiceResultEmpty();
        }

        // find the latest
        $result = $this->findAllByGroup($group, $limit, $page);
        $result->setTotal($count);
        return $result;
    }

    public function countAllByGroup(
        ParentGroup $group
    ): int {
        $qb = $this->getQueryBuilder(self::COMPANY_ENTITY);
        $qb->select('count(' . self::TBL . '.id)')
            ->where('IDENTITY(' . self::TBL . '.parentGroup) = :parent_group_id')
            ->setParameters([
                'parent_group_id' => (string) $group->getId()
            ]);
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findAllByGroup(
        ParentGroup $group,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {
        $qb = $this->getQueryBuilder(self::COMPANY_ENTITY);
        $qb->select(self::TBL)
            ->where('IDENTITY(' . self::TBL . '.parentGroup) = :parent_group_id')
            ->orderBy(self::TBL . '.name', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page))
            ->setParameters([
            'parent_group_id' => (string) $group->getId()
        ]);

        $result = $qb->getQuery()->getResult();
        return $this->getServiceResult($result);
    }

    public function findByID(
        ID $id
    ): ServiceResultInterface {
        $groupTbl = 'g';

        $qb = $this->getQueryBuilder(self::COMPANY_ENTITY);
        $qb->select(self::TBL, $groupTbl)
            ->where(self::TBL . '.id = :id')
            ->leftJoin(self::TBL . '.parentGroup', $groupTbl)
            ->setParameters([
                'id' => $id
            ]);

        $result = $qb->getQuery()->getResult();
        return $this->getServiceResult($result);
    }

    public function findAllInGroups(): ServiceResultInterface {
        $groupTbl = 'g';

        $qb = $this->getQueryBuilder(self::COMPANY_ENTITY);
        $qb->select(self::TBL, $groupTbl)
            ->leftJoin(self::TBL . '.parentGroup', $groupTbl)
            ->addOrderBy($groupTbl . '.name', 'ASC')
            ->addOrderBy(self::TBL . '.name', 'ASC');

        $result = $qb->getQuery()->getResult();
        return $this->getServiceResult($result);
    }
}
