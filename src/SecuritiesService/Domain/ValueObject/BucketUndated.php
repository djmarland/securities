<?php

namespace SecuritiesService\Domain\ValueObject;

use DateTime;

class BucketUndated extends Bucket
{
    public function __construct(DateTime $startDate)
    {
        parent::__construct(
            $startDate,
            null
        );
    }

    public function getName(): string
    {
        return self::UNDATED;
    }

    public function getKey(): string
    {
        return strtolower(self::UNDATED);
    }
}
