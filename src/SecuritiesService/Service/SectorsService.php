<?php

namespace SecuritiesService\Service;

use SecuritiesService\Domain\Entity\Industry;
use SecuritiesService\Domain\Entity\Sector;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\ValueObject\UUID;

class SectorsService extends Service
{
    const SERVICE_ENTITY = 'Sector';

    public function findByUUID(
        UUID $id
    ): Sector {
        $industryTbl = 'i';

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL, $industryTbl)
            ->where(self::TBL . '.id = :id')
            ->leftJoin(self::TBL . '.industry', $industryTbl)
            ->setParameters([
                'id' => $id->getBinary()
            ]);

        $results = $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
        if (empty($results)) {
            throw new EntityNotFoundException;
        }

        return reset($results);
    }

    public function countAll(): int
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('count(' . self::TBL . '.id)');
        return (int) $qb->getQuery()->getSingleScalarResult();
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

    public function countByIndustry(
        Industry $industry
    ): int {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('count(' . self::TBL . '.id)')
            ->where('IDENTITY(' . self::TBL . '.industry) = :industry_id')
            ->setParameters([
                'industry_id' => $industry->getId()->getBinary()
            ]);
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findAllByIndustry(
        Industry $industry,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL)
            ->where('IDENTITY(' . self::TBL . '.industry) = :industry_id')
            ->orderBy(self::TBL . '.name', 'ASC')
            ->setParameters([
                'industry_id' => $industry->getId()->getBinary()
            ]);
        $qb = $this->paginate($qb, $limit, $page);
        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function findAllInIndustries(): array {
        $industryTbl = 'i';

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL, $industryTbl)
            ->leftJoin(self::TBL . '.industry', $industryTbl)
            ->addOrderBy($industryTbl . '.name', 'ASC')
            ->addOrderBy(self::TBL . '.name', 'ASC');

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }
}
