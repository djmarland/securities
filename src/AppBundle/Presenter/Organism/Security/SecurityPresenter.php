<?php

namespace AppBundle\Presenter\Organism\Security;

use SecuritiesService\Domain\Entity\Security;
use AppBundle\Presenter\Presenter;

class SecurityPresenter extends Presenter implements SecurityPresenterInterface
{
    const DATE_FORMAT = 'd/m/Y';

    private $security;

    public function __construct(
        Security $security,
        array $options = [
            'includeLink' => true,
            'showTitle' => true
        ]
    )
    {
        parent::__construct(null, $options);

        $this->security = $security;
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
        return '£' . $this->security->getMoneyRaised() . 'm';
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

    public function getLine():string
    {
        return (string) $this->security->getLine()->getNumber();
    }

    public function getResidualMaturity():string
    {
        $bucket = $this->security->getResidualMaturityBucket();
        return (string) $bucket;
    }

    public function getContractualMaturity():string
    {
        $bucket = $this->security->getContractualMaturityBucket();
        return (string) $bucket;
    }
}