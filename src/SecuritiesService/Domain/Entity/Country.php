<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\ValueObject\ID;
use DateTime;

class Country extends Entity
{
    public function __construct(
        ID $id,
        string $name,
        ParentGroup $parentGroup = null
    ) {
        parent::__construct($id);

        $this->name = $name;
        $this->parentGroup = $parentGroup;
    }

    /**
     * @var string
     */
    private $name;

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @var ParentGroup|null
     */
    private $parentGroup;

    public function getParentGroup()
    {
        return $this->parentGroup;
    }
}
