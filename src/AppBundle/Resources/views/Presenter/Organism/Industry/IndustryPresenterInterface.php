<?php

namespace AppBundle\Presenter\Organism\Industry;

interface IndustryPresenterInterface
{
    public function getID():string;
    public function getName():string;
    public function getLetter():string;
}
