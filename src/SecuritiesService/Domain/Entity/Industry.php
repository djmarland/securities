<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\ValueObject\UUID;

class Industry extends Entity
{
    public function __construct(
        UUID $id,
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
