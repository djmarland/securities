<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\ValueObject\UUID;

class Product extends Entity
{
    public function __construct(
        UUID $id,
        int $number,
        string $name
    ) {
        parent::__construct($id);

        $this->name = $name;
        $this->number = $number;
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
     * @var int
     */
    private $number;

    public function getNumber(): int
    {
        return $this->number;
    }
}
