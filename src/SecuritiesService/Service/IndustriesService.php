<?php

namespace SecuritiesService\Service;

use SecuritiesService\Domain\Entity\Industry;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\ValueObject\ID;

class IndustriesService extends Service
{
    const SERVICE_ENTITY = 'Industry';

    public function findByID(
        ID $id
    ): Industry {
        return parent::simplefindById($id, self::SERVICE_ENTITY);
    }

    public function findAll(): array {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('tbl');
        $qb->orderBy('tbl.name', 'ASC');

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }
}
