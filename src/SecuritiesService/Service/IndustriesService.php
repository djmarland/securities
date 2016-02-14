<?php

namespace SecuritiesService\Service;

use Doctrine\ORM\QueryBuilder;
use SecuritiesService\Domain\ValueObject\ID;

class IndustriesService extends Service
{
    const SERVICE_ENTITY = 'Industry';

    public function findAll(): ServiceResultInterface {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('tbl');
        $qb->orderBy('tbl.name', 'ASC');
        return $this->getServiceResult($qb);
    }

    public function findByID(
        ID $id
    ): ServiceResultInterface {

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL)
            ->where(self::TBL . '.id = :id')
            ->setParameters([
                'id' => $id
            ]);

        return $this->getServiceResult($qb);
    }

    protected function getServiceResult(QueryBuilder $qb, $type = 'Industry')
    {
        return parent::getServiceResult($qb, $type);
    }
}
