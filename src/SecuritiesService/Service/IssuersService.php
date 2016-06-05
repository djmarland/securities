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
        $countryTbl = 'c';
        $groupTbl = 'g';
        $sectorTbl = 's';
        $industryTbl = 'i';

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL, $countryTbl, $groupTbl, $sectorTbl, $industryTbl)
            ->where(self::TBL . '.id = :id')
            ->leftJoin(self::TBL . '.country', $countryTbl)
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

    public function findAllWithoutGroup(): array
    {
        $securitiesTbl = 's';

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select([self::TBL, 'sum(' . $securitiesTbl . '.moneyRaised) as total'])
            ->leftJoin(
                'SecuritiesService:Security',
                $securitiesTbl,
                \Doctrine\ORM\Query\Expr\Join::WITH,
                $securitiesTbl . '.company = ' . self::TBL . '.id'
            )
            ->where(self::TBL . '.parentGroup IS NULL')
            ->groupBy(self::TBL . '.id')
            ->addOrderBy('total', 'DESC')
            ->addOrderBy(self::TBL . '.name', 'ASC');

        $results = $qb->getQuery()->getArrayResult();

        $issuers = [];
        $mapper = $this->mapperFactory->createMapper(self::SERVICE_ENTITY);
        foreach ($results as $result) {
            $issuer = $mapper->getDomainModel($result[0]);
            $total = $result['total'];
            $issuers[] = (object) [
                'issuer' => $issuer,
                'total' => $total,
            ];
        }

        return $issuers;


        /*

        ->leftJoin(self::TBL . '.product', $productTable);

        $qb->groupBy($productTable . '.id');

        // select from companies as c
        // left join securities as s on c.id = s.company_id
        // group by c.id
        // having count(s.id) = 0;

        $securitiesTbl = 's';
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL, ['sum(' . $securitiesTbl . '.moneyRaised)', 'total'])
            ->leftJoin(
                'SecuritiesService:Security',
                $securitiesTbl,
                \Doctrine\ORM\Query\Expr\Join::WITH,
                $securitiesTbl . '.company = ' . self::TBL . '.id'
            )
            ->groupBy(self::TBL . '.id')
            ->having('COUNT(' . $securitiesTbl . '.id) = 0');

        $results = $qb->getQuery()->getArrayResult();

        $issuers = [];
        $mapper = $this->mapperFactory->createMapper(self::SERVICE_ENTITY);
        foreach ($results as $result) {
            $issuer = $mapper->getDomainModel($result[0]);
            $total = $result['total'];
            $issuers[] = (object) [
                'issuer' => $issuer,
                'total' => $total,
            ];
        }

        return $issuers;


         */
    }

    public function findAllWithoutSecurities(): array
    {
        // select from companies as c
        // left join securities as s on c.id = s.company_id
        // group by c.id
        // having count(s.id) = 0;

        $securitiesTbl = 's';
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL)
            ->leftJoin(
                'SecuritiesService:Security',
                $securitiesTbl,
                \Doctrine\ORM\Query\Expr\Join::WITH,
                $securitiesTbl . '.company = ' . self::TBL . '.id'
            )
            ->groupBy(self::TBL . '.id')
            ->having('COUNT(' . $securitiesTbl . '.id) = 0');

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function search(
        string $query,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $countryTbl = 'c';
        $groupTbl = 'g';

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL, $countryTbl, $groupTbl)
            ->leftJoin(self::TBL . '.country', $countryTbl)
            ->leftJoin(self::TBL . '.parentGroup', $groupTbl)
            ->where(self::TBL . '.name LIKE :query')
            ->addOrderBy(self::TBL . '.name', 'ASC')
            ->setParameter('query', '%' . $query . '%');

        $qb = $this->paginate($qb, $limit, $page);
        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function deleteWithId(
        UUID $id
    ): bool {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select()
            ->where(self::TBL . '.id = :id')
            ->setParameter('id', $id->getBinary());
        $entity = $qb->getQuery()->getOneOrNullResult();
        if (!$entity) {
            return false;
        }
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
        return true;
    }
}
