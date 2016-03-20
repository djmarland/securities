<?php

namespace SecuritiesService\Domain\Entity;

use JsonSerializable;
use SecuritiesService\Domain\ValueObject\UUID;

class Product extends Entity implements JsonSerializable
{
    private $name;
    private $number;

    public function __construct(
        UUID $id,
        int $number,
        string $name
    ) {
        parent::__construct($id);

        $this->name = $name;
        $this->number = $number;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function jsonSerialize()
    {
        return (object) [
            'name' => $this->getName(),
            'number' => $this->getNumber(),
        ];
    }
}
