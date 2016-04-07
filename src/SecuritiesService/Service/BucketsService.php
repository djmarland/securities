<?php

namespace SecuritiesService\Service;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use SecuritiesService\Domain\ValueObject\Bucket;
use SecuritiesService\Domain\ValueObject\BucketUndated;

class BucketsService extends Service
{
    public function getAll(): array
    {
        return $this->bucketProvider->findAll();
    }

    public function findByKey(
        string $key
    ): Bucket {
        return $this->bucketProvider->findByKey($key);
    }
}
