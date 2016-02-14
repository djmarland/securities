<?php

namespace SecuritiesService\Service;

use Doctrine\ORM\QueryBuilder;
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
        return $this->getServiceResult($qb);
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

        return $this->getServiceResult($qb);
    }

    public function findByID(
        ID $id
    ): ServiceResultInterface {
        $groupTbl = 'g';
        $sectorTbl = 's';
        $industryTbl = 'i';

        $qb = $this->getQueryBuilder(self::COMPANY_ENTITY);
        $qb->select(self::TBL, $groupTbl, $sectorTbl, $industryTbl)
            ->where(self::TBL . '.id = :id')
            ->leftJoin(self::TBL . '.parentGroup', $groupTbl)
            ->leftJoin($groupTbl . '.sector', $sectorTbl)
            ->leftJoin($sectorTbl . '.industry', $industryTbl)
            ->setParameters([
                'id' => $id
            ]);

        return $this->getServiceResult($qb);
    }

    public function findAllInGroups(): ServiceResultInterface {
        $groupTbl = 'g';

        $qb = $this->getQueryBuilder(self::COMPANY_ENTITY);
        $qb->select(self::TBL, $groupTbl)
            ->leftJoin(self::TBL . '.parentGroup', $groupTbl)
            ->addOrderBy($groupTbl . '.name', 'ASC')
            ->addOrderBy(self::TBL . '.name', 'ASC');

        return $this->getServiceResult($qb);
    }

    public function search(
        string $query,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {
        $qb = $this->getQueryBuilder(self::COMPANY_ENTITY);
        $qb->where(self::TBL . '.name LIKE :query')
            ->addOrderBy(self::TBL . '.name', 'ASC')
            ->setParameters(['query' => '%' . $query . '%']);

        $qb->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));
        return $this->getServiceResult($qb);
    }

    protected function getServiceResult(QueryBuilder $qb, $type = 'Company')
    {
        return parent::getServiceResult($qb, $type);
    }
}
