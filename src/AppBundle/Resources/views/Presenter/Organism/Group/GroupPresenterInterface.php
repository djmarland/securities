<?php

namespace AppBundle\Presenter\Organism\Group;

interface GroupPresenterInterface
{
    public function getID():string;
    public function getName():string;
    public function getLetter():string;
}
