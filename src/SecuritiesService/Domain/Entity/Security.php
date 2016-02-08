<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\ValueObject\Bucket;
use SecuritiesService\Domain\ValueObject\BucketUndated;
use SecuritiesService\Domain\ValueObject\ID;
use SecuritiesService\Domain\ValueObject\ISIN;
use DateTime;

class Security extends Entity
{
    public function __construct(
        ID $id,
        ISIN $isin,
        string $name,
        DateTime $startDate,
        float $moneyRaised,
        Product $product,
        Company $company,
        Currency $currency,
        DateTime $maturityDate = null,
        float $coupon = null
    ) {
        parent::__construct($id);

        $this->name = $name;
        $this->isin = $isin;
        $this->startDate = $startDate;
        $this->currency = $currency;
        $this->moneyRaised = $moneyRaised;
        $this->product = $product;
        $this->company = $company;
        $this->maturityDate = $maturityDate;
        $this->coupon = $coupon;
    }

    /**
     * @var string
     */
    private $name;

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @var string
     */
    private $isin;

    public function getIsin(): string
    {
        return $this->isin;
    }

    /**
     * @var DateTime
     */
    private $startDate;

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    /**
     * @var DateTime|null
     */
    private $maturityDate;

    public function getMaturityDate()
    {
        return $this->maturityDate;
    }

    /**
     * @var float
     */
    private $coupon;

    public function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * @var float
     */
    private $moneyRaised;

    public function getMoneyRaised(): float
    {
        return $this->moneyRaised;
    }

    /**
     * @var Product
     */
    private $product;

    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @var Currency
     */
    private $currency;

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @var Company
     */
    private $company;

    public function getCompany(): Company
    {
        return $this->company;
    }

    private $contractualBucket;

    public function getContractualMaturityBucket()
    {
        if (!$this->contractualBucket) {
            if ($this->maturityDate) {
                $this->contractualBucket = new Bucket($this->startDate, $this->maturityDate);
            } else {
                $this->contractualBucket = new BucketUndated($this->startDate);
            }
        }
        return $this->contractualBucket;
    }

    private $residualBucket;

    public function getResidualMaturityBucketForDate(DateTime $startDate) //@todo - use DateTime immutable everywhere
    {
        if (!$this->residualBucket) {
            if ($this->maturityDate) {
                $this->residualBucket = new Bucket($startDate, $this->maturityDate);
            } else {
                $this->residualBucket = new BucketUndated($startDate);
            }
        }
        return $this->residualBucket;
    }
}
