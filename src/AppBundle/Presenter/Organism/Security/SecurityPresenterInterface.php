<?php

namespace AppBundle\Presenter\Organism\Security;

interface SecurityPresenterInterface
{
    public function getTitle();
    public function getSubH():string;
    public function getISIN():string;
    public function getIssuer():string;
    public function getIssuerID():string;
    public function getAmount():string;
    public function getCurrency():string;
    public function getStartDate():string;
    public function getMaturityDate():string;
    public function getDuration():string;
    public function getCoupon():string;
    public function getLine():string;
    public function getResidualMaturity():string;
    public function getContractualMaturity():string;
}
