<?php
namespace SecuritiesService\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

// @todo - don't use snake case everywhere

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
//    /** @ORM\Column(type="string") */
//    private $market;
//    /** @ORM\Column(type="string") */
//    private $tidm;
//    /** @ORM\Column(type="string") */
//    private $description;
    /** @ORM\Column(type="float") */
    private $moneyRaised;
    /** @ORM\Column(type="date") */
    private $startDate;
    /** @ORM\Column(type="date", nullable=true) */
    private $maturityDate;
    /** @ORM\Column(type="float", nullable=true) */
    private $coupon;
    /** @ORM\ManyToOne(targetEntity="Product") */
    private $product;
    /** @ORM\ManyToOne(targetEntity="Company") */
    private $company;
    /** @ORM\ManyToOne(targetEntity="Currency") */
    private $currency;

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

    public function getTIDM()
    {
        return $this->tidm;
    }

    public function setTIDM($tidm)
    {
        $this->tidm = $tidm;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getIsin()
    {
        return $this->isin;
    }

    public function setIsin($isin)
    {
        $this->isin = $isin;
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

    public function getCoupon()
    {
        return $this->coupon;
    }

    public function setCoupon($coupon)
    {
        $this->coupon = $coupon;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct($product)
    {
        $this->product = $product;
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
}
