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
    public $name;
    /** @ORM\Column(type="string", length=12, unique=true) */
    public $isin;
    /** @ORM\Column(type="string", nullable=true) */
    public $market;
    /** @ORM\Column(type="string", length=255, nullable=true) */
    public $exchange;
    /** @ORM\Column(type="float", nullable=true) */
    public $moneyRaised;
    /** @ORM\Column(type="date", nullable=true) */
    public $startDate;
    /** @ORM\Column(type="date", nullable=true) */
    public $maturityDate;
    /** @ORM\Column(type="string", length=255, nullable=true) */
    public $couponType;
    /** @ORM\Column(type="float", nullable=true) */
    public $coupon;
    /** @ORM\Column(type="float", nullable=true) */
    public $margin;
    /** @ORM\Column(type="string", nullable=true) */
    public $source;
    /** @ORM\Column(type="float", nullable=true) */
    public $moneyRaisedLocal;
    /** @ORM\Column(type="float", nullable=true) */
    public $usdValueNow;
    /** @ORM\Column(type="date", nullable=true) */
    public $usdCalculationDate;
    /** @ORM\Column(type="boolean", nullable=false) */
    public $isInteresting = 0;
    /**
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(nullable=true)
     */
    public $product;
    /**
     * @ORM\ManyToOne(targetEntity="SecurityType")
     * @ORM\JoinColumn(nullable=true)
     */
    public $type;
    /**
     * @ORM\ManyToOne(targetEntity="Company")
     * @ORM\JoinColumn(nullable=true)
     */
    public $company;
    /**
     * @ORM\ManyToOne(targetEntity="Currency")
     * @ORM\JoinColumn(nullable=true)
     */
    public $currency;
    /**
     * @ORM\ManyToMany(targetEntity="Index")
     */
    public $indices;

    public function __construct()
    {
        parent::__construct();
        $this->indices = new ArrayCollection();
    }
}
