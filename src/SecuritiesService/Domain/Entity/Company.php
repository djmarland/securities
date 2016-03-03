<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\Entity\Null\NullParentGroup;
use SecuritiesService\Domain\Exception\DataNotSetException;
use SecuritiesService\Domain\ValueObject\UUID;

class Company extends Entity
{
    private $name;
    private $parentGroup;
    private $country;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrlKey(): string
    {
        return (string) $this->getId();
    }

    public function getParentGroup()
    {
        if ($this->parentGroup instanceof NullParentGroup) {
            return null;
        }
        if (is_null($this->parentGroup)) {
            throw new DataNotSetException('Tried to get parent group data without requesting it up front');
        }
        return $this->parentGroup;
    }

    public function getCountry(): Country
    {
        if (is_null($this->country)) {
            throw new DataNotSetException('Tried to get parent group data without requesting it up front');
        }
        return $this->country;
    }
}
