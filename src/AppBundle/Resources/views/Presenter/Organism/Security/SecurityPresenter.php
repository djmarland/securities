<?php

namespace AppBundle\Presenter\Organism\Security;

use AppBundle\Presenter\Molecule\Money\MoneyPresenter;
use AppBundle\Presenter\Molecule\Money\MoneyPresenterInterface;
use SecuritiesService\Domain\Entity\Security;
use AppBundle\Presenter\Presenter;

class SecurityPresenter extends Presenter implements SecurityPresenterInterface
{
    const DATE_FORMAT = 'd/m/Y';

    protected $options = [
        'template' => null,
    ];

    public function __construct(
        Security $security,
        array $options = []
    ) {
        parent::__construct($security, $options);
        if ($this->options['template']) {
            $this->setTemplateVariation($this->options['template']);
        }
    }

    public function getTitle()
    {
        return $this->getISIN();
    }

    public function getISIN(): string
    {
        return $this->domainModel->getIsin();
    }

    public function getName(): string
    {
        return $this->domainModel->getName();
    }

    public function getExchange(): string
    {
        return $this->domainModel->getExchange();
    }

    public function getAmountRaised(): MoneyPresenterInterface
    {
        $amount = $this->domainModel->getMoneyRaised();
        if ($amount) {
            return new MoneyPresenter($amount);
        }
        $issuedAmount = $this->domainModel->getMoneyRaisedLocal();
        if ($issuedAmount) {
            return new MoneyPresenter(
                $issuedAmount,
                ['currency' => $this->getCurrency()]
            );
        }
        return new MoneyPresenter(0);
    }

    public function getCurrency(): string
    {
        $currency = $this->domainModel->getCurrency();
        if ($currency) {
            return $currency->getCode();
        }
        return 'OTH';
    }

    public function getIssueDate():string
    {
        return $this->domainModel->getStartDate()->format(self::DATE_FORMAT);
    }

    public function getMaturityDate():string
    {
        $date = $this->domainModel->getMaturityDate();
        if ($date) {
            return $this->domainModel->getMaturityDate()->format(self::DATE_FORMAT);
        }
        return 'Undated';
    }

    public function getCoupon(): string
    {
        // coupon values are in decimal, so to display as %
        // we have to multiple by 100
        $coupon = $this->domainModel->getCoupon();
        if ($coupon) {
            return (round($this->domainModel->getCoupon()*100, 2)) . '%';
        }
        return '-';
    }

    public function hasIssuer():bool
    {
        return !!$this->domainModel->getCompany();
    }

    public function getIssuer():string
    {
        if ($this->hasIssuer()) {
            return $this->domainModel->getCompany()->getName();
        }
        return '';
    }

    public function getIssuerID():string
    {
        if ($this->hasIssuer()) {
            return (string) $this->domainModel->getCompany()->getID();
        }
        return '';
    }

    public function hasCountry():bool
    {
        $company = $this->domainModel->getCompany();
        if ($company) {
            return !!$company->getCountry();
        }
        return false;
    }

    public function getCountry():string
    {
        if ($this->hasCountry()) {
            return $this->domainModel->getCompany()->getCountry()->getName();
        }
        return '';
    }

    public function hasParentGroup():bool
    {
        $company = $this->domainModel->getCompany();
        if ($company) {
            return !!$company->getParentGroup();
        }
        return false;
    }

    public function getParentGroup():string
    {
        if ($this->hasParentGroup()) {
            return $this->domainModel->getCompany()
                ->getParentGroup()
                ->getName();
        }
        return '';
    }

    public function getParentGroupID():string
    {
        if ($this->hasParentGroup()) {
            return (string) $this->domainModel->getCompany()
                ->getParentGroup()
                ->getId();
        }
        return '';
    }

    public function hasSector():bool
    {
        $company = $this->domainModel->getCompany();
        if (!$company) {
            return false;
        }
        $group = $company->getParentGroup();
        if (!$group) {
            return false;
        }
        return !!$group->getSector();
    }

    public function getSector():string
    {
        if ($this->hasSector()) {
            return $this->domainModel->getCompany()
                ->getParentGroup()
                ->getSector()
                ->getName();
        }
        return '';
    }

    public function getSectorID():string
    {
        if ($this->hasParentGroup()) {
            return (string) $this->domainModel->getCompany()
                ->getParentGroup()
                ->getSector()
                ->getId();
        }
        return '';
    }

    public function hasIndustry():bool
    {
        $company = $this->domainModel->getCompany();
        if (!$company) {
            return false;
        }
        $group = $company->getParentGroup();
        if (!$group) {
            return false;
        }
        $sector = $group->getSector();
        if (!$sector) {
            return false;
        }
        return !!$sector->getIndustry();
    }

    public function getIndustry():string
    {
        if ($this->hasIndustry()) {
            return $this->domainModel->getCompany()
                ->getParentGroup()
                ->getSector()
                ->getIndustry()
                ->getName();
        }
        return '';
    }

    public function getIndustryID():string
    {
        if ($this->hasParentGroup()) {
            return (string) $this->domainModel->getCompany()
                ->getParentGroup()
                ->getSector()
                ->getIndustry()
                ->getId();
        }
        return '';
    }

    public function getInitialTerm(): string
    {
        $end = $this->domainModel->getMaturityDate();
        if (!$end) {
            return '-';
        }

        $start = $this->domainModel->getStartDate();
        return $this->dateDiff($start, $end);
    }

    public function getRemainingTerm(): string
    {
        $end = $this->domainModel->getMaturityDate();
        if (!$end) {
            return '-';
        }
        $start = new \DateTimeImmutable(); // @todo - inject date
        return $this->dateDiff($start, $end);
    }

    public function getProduct():string
    {
        $product = $this->domainModel->getProduct();
        if ($product) {
            return (string) $product->getName();
        }
        return '-';
    }

    // @todo - be more robust
    private function dateDiff($start, $end)
    {
        $interval = $start->diff($end);

        $years = $interval->y;
        $months = $interval->m;

        $stringParts = [];
        if ($years) {
            $stringParts[] = $years . ' year' . (($years > 1) ? 's' : '');
        }
        if ($months) {
            $stringParts[] = $months . ' month' . (($months > 1) ? 's' : '');
        }

        if (empty($stringParts)) {
            return 'Less than 1 month';
        }
        return implode(', ', $stringParts);
    }
}
