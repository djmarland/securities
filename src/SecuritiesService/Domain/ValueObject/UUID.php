<?php

namespace SecuritiesService\Domain\ValueObject;

use Ramsey\Uuid\Uuid as UuidProvider;
use Ramsey\Uuid\UuidInterface as UuidProviderInterface;
use SecuritiesService\Domain\Exception\ValidationException;

class UUID
{
    private $uuid;

    public function __construct(
        UuidProviderInterface $uuid
    ) {
        $this->uuid = $uuid;
    }

    public static function createFromString($string)
    {
        if (!UuidProvider::isValid($string)) {
            throw new ValidationException('Invalid ID');
        }
        return new self(UuidProvider::fromString($string));
    }

    public function getBinary()
    {
        return $this->uuid->getBytes();
    }

    public function getValue(): UuidProvider
    {
        return $this->uuid;
    }

    public function __toString(): string
    {
        return (string) $this->getValue();
    }
}
