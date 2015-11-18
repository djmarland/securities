<?php
namespace DatabaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity
* @ORM\HasLifecycleCallbacks()
* @ORM\Table(name="securities",indexes={@ORM\Index(name="isin_idx", columns={"isin"})})
*/
class Security extends Entity
{
    /** @ORM\Column(type="string", length=255) */
    private $name;

    /** Getters/Setters */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /** @ORM\Column(type="string", length=12, unique=true) */
    private $isin;

    public function getIsin()
    {
        return $this->isin;
    }

    public function setIsin($isin)
    {
        $this->isin = $isin;
    }

    /** @ORM\Column(type="string", length=4) */
    private $tidm;

    public function getTidm()
    {
        return $this->tidm;
    }

    public function setTidm($tidm)
    {
        $this->tidm = $tidm;
    }

    /** @ORM\Column(type="float") */
    private $money_raised;

    public function getMoneyRaised()
    {
        return $this->money_raised;
    }

    public function setMoneyRaised($money_raised)
    {
        $this->money_raised = $money_raised;
    }

    /** @ORM\Column(type="date") */
    private $start_date;

    public function getStartDate()
    {
        return $this->start_date;
    }

    public function setStartDate($start_date)
    {
        $this->start_date = $start_date;
    }

    /** @ORM\Column(type="date", nullable=true) */
    private $maturity_date;

    public function getMaturityDate()
    {
        return $this->maturity_date;
    }

    public function setMaturityDate($maturity_date)
    {
        $this->maturity_date = $maturity_date;
    }

    /** @ORM\Column(type="float", nullable=true) */
    private $coupon;

    public function getCoupon()
    {
        return $this->coupon;
    }

    public function setCoupon($coupon)
    {
        $this->coupon = $coupon;
    }

    /** @ORM\Column(type="integer", length=6, nullable=true) */
    private $weighting;

    public function getWeighting()
    {
        return $this->weighting;
    }

    public function setWeighting($weighting)
    {
        $this->weighting = $weighting;
    }

    /** @ORM\Column(type="float", nullable=true) */
    private $contractual_maturity;

    public function getContractualMaturity()
    {
        return $this->contractual_maturity;
    }

    public function setContractualMaturity($contractual_maturity)
    {
        $this->contractual_maturity = $contractual_maturity;
    }

    /** @ORM\Column(type="integer") */
    private $issue_month;

    public function getIssueMonth()
    {
        return $this->issue_month;
    }

    public function setIssueMonth($issue_month)
    {
        $this->issue_month = $issue_month;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Fsa04748")
     */
    private $fsa04748;

    public function getFsa04748()
    {
        return $this->fsa04748;
    }

    public function setFsa04748($fsa04748)
    {
        $this->fsa04748 = $fsa04748;
    }

    /**
     * @ORM\ManyToOne(targetEntity="FsaBucket")
     */
    private $fsa_contractual_bucket;

    public function getFsaContractualBucket()
    {
        return $this->fsa_contractual_bucket;
    }

    public function setFsaContractualBucket($fsa_contractual_bucket)
    {
        $this->fsa_contractual_bucket = $fsa_contractual_bucket;
    }

    /**
     * @ORM\ManyToOne(targetEntity="FsaBucket")
     */
    private $fsa_residual_bucket;

    public function getFsaResidualBucket()
    {
        return $this->fsa_residual_bucket;
    }

    public function setFsaResidualBucket($fsa_residual_bucket)
    {
        $this->fsa_residual_bucket = $fsa_residual_bucket;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Market")
     */
    private $market;

    public function getMarket()
    {
        return $this->market;
    }

    public function setMarket($market)
    {
        $this->market = $market;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Company")
     */
    private $company;

    public function getCompany()
    {
        return $this->company;
    }

    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @ORM\ManyToOne(targetEntity="SecurityType")
     */
    private $security_type;

    public function getSecurityType()
    {
        return $this->security_type;
    }

    public function setSecurityType($security_type)
    {
        $this->security_type = $security_type;
    }

    /**
     * @ORM\ManyToOne(targetEntity="MarketSegment")
     */
    private $market_segment;

    public function getMarketSegment()
    {
        return $this->market_segment;
    }

    public function setMarketSegment($market_segment)
    {
        $this->market_segment = $market_segment;
    }

    /**
     * @ORM\ManyToOne(targetEntity="MarketSector")
     */
    private $market_sector;

    public function getMarketSector()
    {
        return $this->market_sector;
    }

    public function setMarketSector($market_sector)
    {
        $this->market_sector = $market_sector;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Currency")
     */
    private $currency;

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }


    /**
     * @ORM\ManyToOne(targetEntity="Country")
     */
    private $country;

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country)
    {
        $this->country = $country;
    }
}
