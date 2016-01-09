<?php

namespace SecuritiesService\Service;

use SecuritiesService\Domain\Entity\Company;
use SecuritiesService\Domain\ValueObject\ID;

class LinesService extends Service
{
    const LINE_ENTITY = 'Line';

    public function findAll(): ServiceResultInterface
    {
        $qb = $this->getQueryBuilder(self::LINE_ENTITY);
        $qb->select('tbl');
        $qb->orderBy('tbl.name', 'ASC');;
        $result = $qb->getQuery()->getResult();
        return $this->getServiceResult($result);
    }

    public function findByID(
        ID $id
    ): ServiceResultInterface {
        $result = $this->entityManager
            ->find('SecuritiesService:Line', $id);
        return $this->getServiceResult($result);
    }

    public function findAllByIssuer(Company $company): ServiceResultInterface
    {
        // @todo - actually make this query (needs to go via "securities, grouped and unique lines"
        return $this->findAll();
    }
}
