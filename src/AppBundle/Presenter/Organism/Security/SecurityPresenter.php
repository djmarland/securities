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
        'template' => null
    ];

    private $security;

    public function __construct(
        Security $security,
        array $options = []
    ) {
        parent::__construct(null, $options);
        $this->security = $security;
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
        return $this->security->getIsin();
    }

    public function getName(): string
    {
        return $this->security->getName();
    }

    public function getIssuer():string
    {
        return $this->security->getCompany()->getName();
    }

    public function getIssuerID():string
    {
        return (string) $this->security->getCompany()->getId();
    }

    public function getAmount():string
    {
        return '£' . number_format(round($this->security->getMoneyRaised(), 1)) . 'm';
    }

    public function getCurrency():string
    {
        return $this->security->getCurrency()->getCode();
    }

    public function getStartDate():string
    {
        return $this->security->getStartDate()->format(self::DATE_FORMAT);
    }

    public function getMaturityDate():string
    {
        $date = $this->security->getMaturityDate();
        if ($date) {
            return $this->security->getMaturityDate()->format(self::DATE_FORMAT);
        }
        return 'UNDATED';
    }

    /**
     * @todo - this should be more robust
     */
    public function getCompany(): Company
    {
        return $this->security->getCompany();
    }

    public function getInitialTerm()
    {
        $start = $this->security->getStartDate();
        $end = $this->security->getMaturityDate();
        return $this->dateDiff($start, $end);
    }

    public function getRemainingTerm()
    {
        $start = new \DateTimeImmutable(); // @todo - inject date
        $end = $this->security->getMaturityDate();
        return $this->dateDiff($start, $end);
    }

    public function getDuration():string
    {
        return '';
    }

    public function getCoupon():string
    {
        $coupon = $this->security->getCoupon();
        if ($coupon) {
            return ($this->security->getCoupon() * 100) . '%';
        }
        return 'N/A';
    }

    public function getProduct():string
    {
        return (string) $this->security->getProduct()->getName();
    }

    public function getResidualMaturity():string
    {
        $bucket = $this->security->getResidualMaturityBucketForDate(new \DateTime()); // @todo - inject app time
        return (string) $bucket;
    }

    public function getContractualMaturity():string
    {
        $bucket = $this->security->getContractualMaturityBucket();
        return (string) $bucket;
    }

    // @todo - be more robust
    private function dateDiff($start, $end)
    {
        $end = $end->getTimestamp();
        $start = $start->getTimestamp();
        $diff = $end - $start;

        $year = 60*60*24*365;
        $month = 60*60*24*29.8;
        $years = (floor($diff/$year));
        $remaining = $diff - ($years * $year);
        $months = floor($remaining/$month);

        $stringParts = [];
        if ($years) {
            $stringParts[] = $years . ' years';
        }
        if ($months) {
            $stringParts[] = $months . ' months';
        }

        return implode(', ', $stringParts);
    }
}
