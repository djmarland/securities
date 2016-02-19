<?php

namespace SecuritiesService\Service;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use SecuritiesService\Domain\Entity\Company;
use SecuritiesService\Domain\Entity\Product;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\ValueObject\ID;

class ProductsService extends Service
{
    const SERVICE_ENTITY = 'Product';

    public function findByID(
        ID $id
    ): Product {
        return parent::findById($id, self::SERVICE_ENTITY);
    }

    public function findAll(): array
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('tbl');
        $qb->orderBy('tbl.name', 'ASC');
        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function findAllByIssuer(Company $company): array
    {
        // @todo - actually make this query (needs to go via "securities, grouped and unique products"
        return $this->findAll();
    }
}
