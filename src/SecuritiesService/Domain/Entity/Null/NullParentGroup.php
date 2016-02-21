<?php

namespace SecuritiesService\Domain\Entity\Null;

use SecuritiesService\Domain\Entity\ParentGroup;

/**
 * This class is used when a parent group was called,
 * but it legitimately does not have one set.
 */
final class NullParentGroup extends ParentGroup
{

    public function __construct()
    {
    }
}
