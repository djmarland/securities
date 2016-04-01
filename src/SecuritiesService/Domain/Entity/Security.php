<?php

namespace SecuritiesService\Domain\Entity;

use JsonSerializable;
use SecuritiesService\Domain\ValueObject\Bucket;
use SecuritiesService\Domain\ValueObject\BucketUndated;
use SecuritiesService\Domain\ValueObject\UUID;
use SecuritiesService\Domain\ValueObject\ISIN;
use DateTime;

class Security extends Entity implements JsonSerializable
{
    private $name;
    private $isin;
    private $startDate;
    private $maturityDate;
    private $coupon;
    private $moneyRaised;
    private $product;
    private $currency;
    private $company;
    private $exchange;

    public function __construct(
        UUID $id,
        ISIN $isin,
        string $name,
        string $exchange,
        DateTime $startDate,
        float $moneyRaised,
        Product $product = null,
        Company $company = null,
        Currency $currency = null,
        DateTime $maturityDate = null,
        float $coupon = null
    ) {
        parent::__construct($id);

        $this->name = $name;
        $this->exchange = $exchange;
        $this->isin = $isin;
        $this->startDate = $startDate;
        $this->currency = $currency;
        $this->moneyRaised = $moneyRaised;
        $this->product = $product;
        $this->company = $company;
        $this->maturityDate = $maturityDate;
        $this->coupon = $coupon;
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function getExchange(): string
    {
        return $this->exchange;
    }

    public function getIsin(): string
    {
        return $this->isin;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function getMaturityDate()
    {
        return $this->maturityDate;
    }

    public function getCoupon()
    {
        return $this->coupon;
    }

    public function getMoneyRaised(): float
    {
        return $this->moneyRaised;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function jsonSerialize()
    {
        $dateFormat = 'd/m/Y';

        return (object) [
            'isin' => $this->getIsin(),
            'name' => $this->getName(),
            'exchange' => $this->getExchange(),
            'startDate' => $this->getStartDate()->format($dateFormat),
            'maturityDate' => $this->getMaturityDate() ? $this->getMaturityDate()->format($dateFormat) : null,
            'coupon' => $this->getCoupon(),
            'amountRaised' => $this->getMoneyRaised(),
            'currency' => $this->getCurrency()->getCode(),
            'product' => $this->getProduct(),
            'issuer' => $this->getCompany(),
        ];
    }
}
