<?php

namespace AppBundle\Presenter\Organism\Security;

use AppBundle\Presenter\Molecule\Money\MoneyPresenterInterface;

interface SecurityPresenterInterface
{
    public function getTitle();
    public function getISIN():string;
    public function getAmountRaised():MoneyPresenterInterface;
    public function getCurrency():string;
    public function getIssueDate():string;
    public function getMaturityDate():string;

    public function hasCountry():bool;
    public function getCountry():string;

    public function getCoupon():string;
    public function getProduct():string;

    public function hasIssuer():bool;
    public function getIssuer():string;
    public function getIssuerID():string;

    public function hasParentGroup():bool;
    public function getParentGroup():string;
    public function getParentGroupId():string;

    public function hasSector():bool;
    public function getSector():string;
    public function getSectorId():string;

    public function hasIndustry():bool;
    public function getIndustry():string;
    public function getIndustryId():string;

    public function getInitialTerm():string;
    public function getRemainingTerm():string;
}
