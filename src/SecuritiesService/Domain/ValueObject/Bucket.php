<?php

namespace SecuritiesService\Domain\ValueObject;

use DateTimeImmutable;

class Bucket
{
    private $startDate;
    private $endDate;
    private $name;
    private $key;

    public function __construct(
        DateTimeImmutable $startDate,
        string $name,
        string $key,
        DateTimeImmutable $endDate = null
    ) {
        $this->startDate = $startDate;
        $this->name = $name;
        $this->key = $key;
        $this->endDate = $endDate;
    }


    public function getKey(): string
    {
        return $this->key;
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function __toString()
    {
        return (string) $this->getName();
    }
}
