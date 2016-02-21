<?php

namespace SecuritiesService\Domain\Entity\Null;

use SecuritiesService\Domain\Entity\Sector;

/**
 * This class is used when a parent group was called,
 * but it legitimately does not have one set.
 */
final class NullSector extends Sector
{
    public function __construct()
    {
    }
}
