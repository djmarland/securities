<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\ValueObject\UUID;
use DateTime;

class Country extends Entity
{
    private $name;

    public function __construct(
        UUID $id,
        string $name
    ) {
        parent::__construct($id);

        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
