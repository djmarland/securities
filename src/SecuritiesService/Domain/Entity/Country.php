<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\ValueObject\UUID;
use DateTime;

class Country extends Entity
{
    private $name;
    private $parentGroup;

    public function __construct(
        UUID $id,
        string $name,
        ParentGroup $parentGroup = null
    ) {
        parent::__construct($id);

        $this->name = $name;
        $this->parentGroup = $parentGroup;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParentGroup()
    {
        return $this->parentGroup;
    }
}
