<?php

namespace SecuritiesService\Service;

use Doctrine\ORM\Query;

use SecuritiesService\Domain\Entity\Company;
use SecuritiesService\Domain\Entity\Product;
use SecuritiesService\Domain\ValueObject\UUID;

class ProductsService extends Service
{
    const SERVICE_ENTITY = 'Product';

    public function findByID(
        UUID $id
    ): Product {
        return parent::simpleFindByUUID($id, self::SERVICE_ENTITY);
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
