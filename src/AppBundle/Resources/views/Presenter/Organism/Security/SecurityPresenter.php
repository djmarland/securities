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

    public function getExchange(): string
    {
        return $this->security->getExchange();
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
        $val = number_format($this->security->getMoneyRaised(), 2);
        return '£' . $val . 'm';
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
        return 'Undated';
    }

    /**
     * @todo - this should be more robust
     */
    public function getCompany(): Company
    {
        return $this->security->getCompany();
    }

    public function getInitialTerm(): string
    {
        $end = $this->security->getMaturityDate();
        if (!$end) {
            return '-';
        }

        $start = $this->security->getStartDate();
        return $this->dateDiff($start, $end);
    }

    public function getRemainingTerm(): string
    {
        $end = $this->security->getMaturityDate();
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
        $coupon = $this->security->getCoupon();
        if ($coupon) {
            return (round($this->security->getCoupon()*100, 2)) . '%';
        }
        return '-';
    }

    public function getProduct():string
    {
        return (string) $this->security->getProduct()->getName();
    }

    public function getResidualMaturity(): string
    {
        $bucket = $this->security->getResidualMaturityBucketForDate(new \DateTime()); // @todo - inject app time
        return (string) $bucket;
    }

    public function getContractualMaturity(): string
    {
        $bucket = $this->security->getContractualMaturityBucket();
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
