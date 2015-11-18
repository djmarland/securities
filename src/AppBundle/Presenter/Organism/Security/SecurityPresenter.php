<?php

namespace AppBundle\Presenter\Organism\Security;

use AppBundle\Domain\Entity\Security;
use AppBundle\Presenter\Presenter;

class SecurityPresenter extends Presenter implements SecurityPresenterInterface
{

    private $security;

    public function __construct(
        Security $security,
        array $options = [
            'showTitle' => true
        ]
    )
    {
        parent::__construct(null, $options);

        $this->security = $security;
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
        return '';
    }

    public function getAmount():string
    {
        return '';
    }

    public function getCurrency():string
    {
        return '';
    }

    public function getStartDate():string
    {
        return '';
    }

    public function getMaturityDate():string
    {
        return '';
    }

    public function getDuration():string
    {
        return '';
    }

    public function getCoupon():string
    {
        return '';
    }

    public function getFsa047Line():string
    {
        return '';
    }

    public function getFas047Name():string
    {
        return '';
    }

    public function getResidualMaturity():string
    {
        return '';
    }

    public function getContractualMaturity():string
    {
        return '';
    }
}