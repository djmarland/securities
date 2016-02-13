<?php

namespace AppBundle\Presenter\Organism\Sector;

interface SectorPresenterInterface
{
    public function getID():string;
    public function getName():string;
    public function getLetter():string;
}
