<?php

namespace SecuritiesService\Domain\ValueObject;

class UUID
{

    /**
     * @param $uuid
     */
    public function __construct(
        string $uuid
    ) {
        $this->uuid = $uuid;
    }

    /**
     * @var int
     */
    private $uuid;

    public function getValue(): int
    {
        return $this->uuid;
    }

    public function __toString(): string
    {
        return (string) $this->getValue();
    }
}
