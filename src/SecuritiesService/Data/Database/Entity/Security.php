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

    /**
     * @ORM\ManyToOne(targetEntity="Product")
     */
    private $product;

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct($product)
    {
        $this->product = $product;
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
}
