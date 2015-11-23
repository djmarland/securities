<?php

namespace AppBundle\Domain\Entity;

use AppBundle\Domain\ValueObject\ID;
use AppBundle\Domain\ValueObject\ISIN;
use DateTime;

class Security extends Entity
{

    /**
     * @param ID $id
     * @param DateTime $createdAt
     * @param DateTime $updatedAt
     * @param ISIN $isin
     * @param string $name
     * @param DateTime $startDate
     * @param float $moneyRaised
     * @param Company $company
     * @param Currency $currency
     * @param DateTime $maturityDate
     * @param float $coupon
     */
    public function __construct(
        ID $id,
        DateTime $createdAt,
        DateTime $updatedAt,
        ISIN $isin,
        string $name,
        DateTime $startDate,
        float $moneyRaised,
        Fsa04748 $fsa04748,
        Company $company,
        Currency $currency,
        DateTime $maturityDate = null,
        float $coupon = null
    ) {
        parent::__construct(
            $id,
            $createdAt,
            $updatedAt
        );

        $this->name = $name;
        $this->isin = $isin;
        $this->startDate = $startDate;
        $this->currency = $currency;
        $this->moneyRaised = $moneyRaised;
        $this->fsa04748 = $fsa04748;
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
     * @var string
     */
    private $moneyRaised;

    public function getMoneyRaised(): float
    {
        return $this->moneyRaised;
    }

    /**
     * @var string
     */
    private $fsa04748;

    public function getFsa04748(): Fsa04748
    {
        return $this->fsa04748;
    }

    /**
     * @var string
     */
    private $currency;

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @var string
     */
    private $company;

    public function getCompany(): Company
    {
        return $this->company;
    }
}
