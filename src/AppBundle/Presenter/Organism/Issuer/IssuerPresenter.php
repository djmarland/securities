<?php

namespace AppBundle\Presenter\Organism\Issuer;

use SecuritiesService\Domain\Entity\Company;
use AppBundle\Presenter\Presenter;

class IssuerPresenter extends Presenter implements IssuerPresenterInterface
{

    private $issuer;

    public function __construct(
        Company $issuer,
        array $options = []
    ) {
        parent::__construct(null, $options);
        $this->issuer = $issuer;
    }

    public function getName():string
    {
        return $this->issuer->getName();
    }

    public function getID():string
    {
        return (string) $this->issuer->getId();
    }

    public function getLetter():string
    {
        $first = substr($this->getName(), 0, 1);
        if (ctype_alpha($first)) {
            return strtoupper($first);
        }
        return '#';
    }
}
