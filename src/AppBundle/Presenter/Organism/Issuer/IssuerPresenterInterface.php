<?php

namespace AppBundle\Presenter\Organism\Issuer;

interface IssuerPresenterInterface
{
    public function getID():string;
    public function getName():string;
    public function getLetter():string;
}
