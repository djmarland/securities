<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\ValueObject\ID;
use DateTime;

class ParentGroup extends Entity
{
    public function __construct(
        ID $id,
        string $name
    ) {
        parent::__construct($id);

        $this->name = $name;
    }

    /**
     * @var string
     */
    private $name;

    public function getName(): string
    {
        return $this->name;
    }
}
