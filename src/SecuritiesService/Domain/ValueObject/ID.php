<?php

namespace SecuritiesService\Domain\ValueObject;

class ID
{

    /**
     * @param $id
     */
    public function __construct(
        int $id
    ) {
        $this->id = $id;
    }

    /**
     * @var int
     */
    private $id;

    public function getValue(): int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->getValue();
    }
}
