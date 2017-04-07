<?php

namespace SecuritiesService\Data\Database\Mapper;

use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\Security;
use SecuritiesService\Domain\ValueObject\ISIN;
use SecuritiesService\Domain\ValueObject\UUID;

class SecurityMapper extends Mapper
{
    public function getDomainModel(array $item): Entity
    {
        $id = new UUID($item['id']);
        $isin = new ISIN($item['isin']);
        $product = null;
        $company = null;
        $currency = null;

        if (isset($item['product'])) {
            $product = $this->mapperFactory->createProduct()->getDomainModel($item['product']);
        }

        if (isset($item['company'])) {
            $company = $this->mapperFactory->createCompany()->getDomainModel($item['company']);
        }

        if (isset($item['currency'])) {
            $currency = $this->mapperFactory->createCurrency()->getDomainModel($item['currency']);
        }

        $security = new Security(
            $id,
            $isin,
            $item['name'],
            $item['startDate'],
            !!$item['isInteresting'],
            $item['moneyRaised'],
            $item['usdValueNow'],
            $item['moneyRaisedLocal'],
            $product,
            $company,
            $currency,
            $item['maturityDate'],
            $item['coupon'],
            $item['margin'],
            $item['source']
        );
        return $security;
    }
}
