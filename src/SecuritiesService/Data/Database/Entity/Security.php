<?php
namespace SecuritiesService\Data\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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

    /** @ORM\Column(type="string", length=12, unique=true) */
    private $isin;

    /** @ORM\Column(type="string", nullable=true) */
    private $market;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $exchange;

    /** @ORM\Column(type="float", nullable=true) */
    private $moneyRaised;

    /** @ORM\Column(type="date", nullable=true) */
    private $startDate;

    /** @ORM\Column(type="date", nullable=true) */
    private $maturityDate;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $couponType;

    /** @ORM\Column(type="float", nullable=true) */
    private $coupon;

    /** @ORM\Column(type="float", nullable=true) */
    private $margin;

    /** @ORM\Column(type="string", nullable=true) */
    private $source;

    /** @ORM\Column(type="float", nullable=true) */
    private $moneyRaisedLocal;

    /**
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(nullable=true)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="SecurityType")
     * @ORM\JoinColumn(nullable=true)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Company")
     * @ORM\JoinColumn(nullable=true)
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity="Currency")
     * @ORM\JoinColumn(nullable=true)
     */
    private $currency;

    /**
     * @ORM\ManyToMany(targetEntity="Index")
     */
    private $indices;

    public function __construct()
    {
        parent::__construct();
        $this->indices = new ArrayCollection();
    }

    /** Getters/Setters */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getMarket()
    {
        return $this->market;
    }

    public function setMarket($market)
    {
        $this->market = $market;
    }

    public function getIsin()
    {
        return $this->isin;
    }

    public function setIsin($isin)
    {
        $this->isin = $isin;
    }

    public function getExchange()
    {
        return $this->exchange;
    }

    public function setExchange($exchange)
    {
        $this->exchange = $exchange;
    }

    public function getMoneyRaised()
    {
        return $this->moneyRaised;
    }

    public function setMoneyRaised($moneyRaised)
    {
        $this->moneyRaised = $moneyRaised;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    public function getMaturityDate()
    {
        return $this->maturityDate;
    }

    public function setMaturityDate($maturityDate)
    {
        $this->maturityDate = $maturityDate;
    }

    public function getCouponType()
    {
        return $this->couponType;
    }

    public function setCouponType($couponType)
    {
        $this->couponType = $couponType;
    }

    public function getCoupon()
    {
        return $this->coupon;
    }

    public function setCoupon($coupon)
    {
        $this->coupon = $coupon;
    }

    public function getMargin()
    {
        return $this->margin;
    }

    public function setMargin($margin)
    {
        $this->margin = $margin;
    }

    public function setSource($source)
    {
        $this->source = $source;
    }

    public function getMoneyRaisedLocal()
    {
        return $this->moneyRaisedLocal;
    }

    public function setMoneyRaisedLocal($moneyRaisedLocal)
    {
        $this->moneyRaisedLocal = $moneyRaisedLocal;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct($product)
    {
        $this->product = $product;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function setCompany($company)
    {
        $this->company = $company;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getIndices()
    {
        return $this->indices;
    }

    public function setVersionTypes($indices)
    {
        $this->indices = $indices;
    }
}
