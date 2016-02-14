<?php

namespace SecuritiesService\Service;

use Doctrine\ORM\QueryBuilder;
use SecuritiesService\Domain\Entity\Sector;
use SecuritiesService\Domain\ValueObject\ID;

class GroupsService extends Service
{
    const SERVICE_ENTITY = 'ParentGroup';

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
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('count(tbl.id)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findAll(
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): ServiceResultInterface {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('tbl');
        $qb->orderBy('tbl.name', 'ASC');
        $qb->setMaxResults($limit)
            ->setFirstResult($this->getOffset($limit, $page));
        return $this->getServiceResult($qb);
    }

    public function findAllBySector(
        Sector $sector
    ): ServiceResultInterface {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL)
            ->where('IDENTITY(' . self::TBL . '.sector) = :sector_id')
            ->orderBy(self::TBL . '.name', 'ASC')
            ->setParameters([
                'sector_id' => (string) $sector->getId()
            ]);

        return $this->getServiceResult($qb);
    }

    public function findByID(
        ID $id
    ): ServiceResultInterface {
        $sectorTbl = 's';
        $industryTbl = 'i';

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL, $sectorTbl, $industryTbl)
            ->where(self::TBL . '.id = :id')
            ->leftJoin(self::TBL . '.sector', $sectorTbl)
            ->leftJoin($sectorTbl . '.industry', $industryTbl)
            ->setParameters([
                'id' => (string) $id
            ]);

        return $this->getServiceResult($qb);
    }


    public function findAllInSectors(): ServiceResultInterface {
        $sectorTbl = 's';

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL, $sectorTbl)
            ->leftJoin(self::TBL . '.sector', $sectorTbl)
            ->addOrderBy($sectorTbl . '.name', 'ASC')
            ->addOrderBy(self::TBL . '.name', 'ASC');

        return $this->getServiceResult($qb);
    }

    protected function getServiceResult(QueryBuilder $qb, $type = 'ParentGroup')
    {
        return parent::getServiceResult($qb, $type);
    }
}
