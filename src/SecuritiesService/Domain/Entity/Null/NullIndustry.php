<?php

namespace SecuritiesService\Domain\Entity\Null;

use SecuritiesService\Domain\Entity\Industry;

/**
 * This class is used when an industry was called,
 * but it legitimately does not have one set.
 */
final class NullIndustry extends Industry
{
    public function __construct()
    {
    }
}
