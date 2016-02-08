<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\Security;
use SecuritiesService\Domain\ValueObject\ISIN;
use SecuritiesService\Domain\ValueObject\ID;

class SecurityMapper extends Mapper
{
    public function getDomainModel(array $item): Entity
    {
        $id = new ID($item['id']);
        $isin = new ISIN($item['isin']);
        $product = $this->mapperFactory->createProduct()->getDomainModel($item['product']);
        $company = $this->mapperFactory->createCompany()->getDomainModel($item['company']);
        $currency = $this->mapperFactory->createCurrency()->getDomainModel($item['currency']);

        $security = new Security(
            $id,
            $isin,
            $item['name'],
            $item['start_date'],
            $item['money_raised'],
            $product,
            $company,
            $currency,
            $item['maturity_date'],
            $item['coupon']
        );
        return $security;
    }
}
