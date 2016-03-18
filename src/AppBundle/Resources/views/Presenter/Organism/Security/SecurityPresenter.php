<?php

namespace AppBundle\Presenter\Organism\Security;

use SecuritiesService\Domain\Entity\Company;
use SecuritiesService\Domain\Entity\Country;
use SecuritiesService\Domain\Entity\Security;
use AppBundle\Presenter\Presenter;

class SecurityPresenter extends Presenter implements SecurityPresenterInterface
{
    const DATE_FORMAT = 'd/m/Y';

    protected $options = [
        'includeLink' => true,
        'showTitle' => true,
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

    public function includeLink()
    {
        return $this->getOptions()->includeLink;
    }

    public function getTitle()
    {
        if ($this->options['showTitle']) {
            return $this->getISIN();
        }
        return null;
    }

    public function getSubH():string
    {
        if ($this->getTitle()) {
            return 'h3';
        }
        return 'h2';
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

    public function hasIssuer():bool
    {
        return !!$this->domainModel->getCompany();
    }

    public function getIssuer():string
    {
        $company = $this->domainModel->getCompany();
        if ($company) {
            return $company->getName();
        }
        return  '';
    }

    public function getIssuerID():string
    {
        $company = $this->domainModel->getCompany();
        if ($company) {
            return (string) $company->getId();
        }
        return  '';
    }

    public function getAmount():string
    {
        $val = number_format($this->domainModel->getMoneyRaised(), 2);
        return '£' . $val . 'm';
    }

    public function getCurrency():string
    {
        return $this->domainModel->getCurrency()->getCode();
    }

    public function getStartDate():string
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

    /**
     * @todo - this should be more robust
     */
    public function getCompany(): Company
    {
        return $this->domainModel->getCompany();
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

    public function getDuration(): string
    {
        return '';
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

    public function getProduct():string
    {
        return (string) $this->domainModel->getProduct()->getName();
    }

    public function getProductNumber():string
    {
        return (string) $this->domainModel->getProduct()->getNumber();
    }

    public function getResidualMaturity(): string
    {
        $bucket = $this->domainModel->getResidualMaturityBucketForDate(new \DateTime()); // @todo - inject app time
        return (string) $bucket;
    }

    public function getContractualMaturity(): string
    {
        $bucket = $this->domainModel->getContractualMaturityBucket();
        return (string) $bucket;
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
