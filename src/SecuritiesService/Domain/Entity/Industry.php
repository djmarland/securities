<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\ValueObject\UUID;

class Industry extends Entity implements \JsonSerializable
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

    public function jsonSerialize() {
        return (object) [
            'id' => $this->getId(),
            'name' => $this->getName()
        ];
    }
}
