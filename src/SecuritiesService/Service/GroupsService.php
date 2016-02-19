<?php

namespace SecuritiesService\Service;

use SecuritiesService\Domain\Entity\ParentGroup;
use SecuritiesService\Domain\Entity\Sector;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\ValueObject\ID;

class GroupsService extends Service
{
    const SERVICE_ENTITY = 'ParentGroup';

    public function findByID(
        ID $id
    ): ParentGroup {
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

        $results = $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
        if (empty($results)) {
            throw new EntityNotFoundException;
        }

        return reset($results);
    }

    public function findAllBySector(
        Sector $sector
    ): array {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL)
            ->where('IDENTITY(' . self::TBL . '.sector) = :sector_id')
            ->orderBy(self::TBL . '.name', 'ASC')
            ->setParameters([
                'sector_id' => (string) $sector->getId()
            ]);

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }


    public function findAllInSectors(): array {
        $sectorTbl = 's';

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL, $sectorTbl)
            ->leftJoin(self::TBL . '.sector', $sectorTbl)
            ->addOrderBy($sectorTbl . '.name', 'ASC')
            ->addOrderBy(self::TBL . '.name', 'ASC');

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

}
