<?php

namespace SecuritiesService\Data\Database\Mapper;

/**
 * Factory to create mappers as needed
 */
class MapperFactory
{
    public function createMapper(string $type): MapperInterface
    {
        $mapperMethod = 'create' . $type;
        if (!method_exists($this, $mapperMethod)) {
            throw new \InvalidArgumentException('Unexpected data type');
        }
        return $this->$mapperMethod();
    }

    public function createCompany(): CompanyMapper
    {
        return new CompanyMapper($this);
    }

    public function createConfig(): ConfigMapper
    {
        return new ConfigMapper($this);
    }

    public function createCountry(): CountryMapper
    {
        return new CountryMapper($this);
    }

    public function createCurrency(): CurrencyMapper
    {
        return new CurrencyMapper($this);
    }

    public function createExchangeRate(): ExchangeRateMapper
    {
        return new ExchangeRateMapper($this);
    }

    public function createIndustry(): IndustryMapper
    {
        return new IndustryMapper($this);
    }

    public function createParentGroup(): ParentGroupMapper
    {
        return new ParentGroupMapper($this);
    }

    public function createProduct(): ProductMapper
    {
        return new ProductMapper($this);
    }

    public function createSecurity(): SecurityMapper
    {
        return new SecurityMapper($this);
    }

    public function createSector(): SectorMapper
    {
        return new SectorMapper($this);
    }

    public function createUser(): UserMapper
    {
        return new UserMapper($this);
    }

    public function createYieldCurve(): YieldCurveMapper
    {
        return new YieldCurveMapper($this);
    }
}
