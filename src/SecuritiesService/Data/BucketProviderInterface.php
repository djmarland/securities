<?php

namespace SecuritiesService\Data;

use SecuritiesService\Domain\ValueObject\Bucket;

interface BucketProviderInterface
{
    public function findAll(): array;
    public function findByKey($key): Bucket;
}
