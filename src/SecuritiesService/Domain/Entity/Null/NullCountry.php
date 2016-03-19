<?php

namespace SecuritiesService\Domain\Entity\Null;

use SecuritiesService\Domain\Entity\Country;

/**
 * This class is used when a country was called,
 * but it legitimately does not have one set.
 */
final class NullCountry extends Country
{
    public function __construct()
    {
    }
}
