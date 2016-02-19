<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\ValueObject\UUID;

class Company extends Entity
{
    public function __construct(
        UUID $id,
        string $name,
        Country $country = null,
        ParentGroup $parentGroup = null
    ) {
        parent::__construct($id);

        $this->name = $name;
        $this->parentGroup = $parentGroup;
        $this->country = $country;
    }

    private $name;

    public function getName(): string
    {
        return $this->name;
    }

    private $parentGroup;

    public function getParentGroup(): ParentGroup
    {
        if (is_null($this->parentGroup)) {
            // @todo, use a proper exception
            throw new \Exception('Tried to get parent group data without requesting it up front');
        }
        return $this->parentGroup;
    }

    /**
     * @var ParentGroup|null
     */
    private $country;

    public function getCountry(): Country
    {
        if (is_null($this->country)) {
            // @todo, use a proper exception
            throw new \Exception('Tried to get country data without requesting it up front');
        }
        return $this->country;
    }
}
