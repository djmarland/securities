<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\ValueObject\UUID;

/**
 * Class Entity
 * For those which the base object inherit
 */
abstract class Entity
{
    const KEY_PREFIX = null;

    protected $id;

    public function __construct(
        UUID $id
    ) {
        $this->id = $id;
    }


    public function getId(): UUID
    {
        return $this->id;
    }
}
