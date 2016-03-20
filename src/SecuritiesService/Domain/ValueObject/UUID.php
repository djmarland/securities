<?php

namespace SecuritiesService\Domain\ValueObject;

use Ramsey\Uuid\Uuid as UuidProvider;
use Ramsey\Uuid\UuidInterface as UuidProviderInterface;
use SecuritiesService\Domain\Exception\ValidationException;

class UUID implements \JsonSerializable
{
    private $uuid;

    public function __construct(
        UuidProviderInterface $uuid
    ) {
        $this->uuid = $uuid;
    }

    public static function createFromString($string)
    {
        $valid = UuidProvider::isValid($string);
        if (!$valid) {
            throw new ValidationException('Invalid ID');
        }
        return new self(UuidProvider::fromString($string));
    }

    public function getBinary()
    {
        return $this->uuid->getBytes();
    }

    public function getValue(): UuidProviderInterface
    {
        return $this->uuid;
    }

    public function __toString(): string
    {
        return (string) $this->getValue();
    }


    public function jsonSerialize()
    {
        return $this->__toString();
    }
}
