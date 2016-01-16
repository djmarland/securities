<?php

namespace SecuritiesService\Service;

use SecuritiesService\Domain\Entity\Company;
use SecuritiesService\Domain\ValueObject\ID;

class ProductsService extends Service
{
    const PRODUCT_ENTITY = 'Product';

    public function findAll(): ServiceResultInterface
    {
        $qb = $this->getQueryBuilder(self::PRODUCT_ENTITY);
        $qb->select('tbl');
        $qb->orderBy('tbl.name', 'ASC');;
        $result = $qb->getQuery()->getResult();
        return $this->getServiceResult($result);
    }

    public function findByID(
        ID $id
    ): ServiceResultInterface {
        $result = $this->entityManager
            ->find('SecuritiesService:Product', $id);
        return $this->getServiceResult($result);
    }

    public function findAllByIssuer(Company $company): ServiceResultInterface
    {
        // @todo - actually make this query (needs to go via "securities, grouped and unique products"
        return $this->findAll();
    }
}
