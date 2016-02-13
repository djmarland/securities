<?php

namespace AppBundle\Presenter\Organism\Industry;

use SecuritiesService\Domain\Entity\Industry;
use AppBundle\Presenter\Presenter;

class IndustryPresenter extends Presenter implements IndustryPresenterInterface
{

    private $industry;

    private $sectors;

    public function __construct(
        Industry $industry,
        array $sectors,
        array $options = [
        ]
    )
    {
        parent::__construct(null, $options);

        $this->industry = $industry;
        $this->sectors = $sectors;
    }

    public function getName():string
    {
        return $this->industry->getName();
    }

    public function getSectors():array
    {
        return $this->sectors;
    }

    public function getID():string
    {
        return (string) $this->industry->getId();
    }

    public function getLetter():string
    {
        return substr($this->getName(), 0, 1);
    }
}