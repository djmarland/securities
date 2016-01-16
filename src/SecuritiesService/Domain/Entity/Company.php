<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\ValueObject\ID;
use DateTime;

class Company extends Entity
{
    public function __construct(
        ID $id,
        DateTime $createdAt,
        DateTime $updatedAt,
        string $name,
        ParentGroup $parentGroup = null
    ) {
        parent::__construct(
            $id,
            $createdAt,
            $updatedAt
        );

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
