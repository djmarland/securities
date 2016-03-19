<?php

namespace SecuritiesService\Domain\Entity;

use JsonSerializable;
use SecuritiesService\Domain\Entity\Null\NullCountry;
use SecuritiesService\Domain\Entity\Null\NullParentGroup;
use SecuritiesService\Domain\Exception\DataNotSetException;
use SecuritiesService\Domain\ValueObject\UUID;

class Company extends Entity implements JsonSerializable
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

    public function getCountry()
    {
        if ($this->country instanceof NullCountry) {
            return null;
        }
        if (is_null($this->country)) {
            throw new DataNotSetException('Tried to get country data without requesting it up front');
        }
        return $this->country;
    }

    public function jsonSerialize() {
        return (object) [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'parentGroup' => $this->getParentGroup(),
            'country' => $this->getCountry() ? $this->getCountry()->getName() : null,
        ];
    }
}
