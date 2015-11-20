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
     * @param DateTime $startTime
     * @param float $moneyRaised
     * @param Currency $currency
     */
    public function __construct(
        ID $id,
        DateTime $createdAt,
        DateTime $updatedAt,
        ISIN $isin,
        string $name,
        DateTime $startDate,
        float $moneyRaised,
        Currency $currency,
        DateTime $maturityDate = null
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
        $this->maturityDate = $maturityDate;
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
    private $currency;

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}
