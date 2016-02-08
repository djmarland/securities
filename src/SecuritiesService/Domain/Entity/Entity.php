<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\ValueObject\ID;
use SecuritiesService\Domain\ValueObject\IDUnset;
use SecuritiesService\Domain\ValueObject\Key;
use DateTime;

/**
 * Class Entity
 * For those which the base object inherit
 */
abstract class Entity
{
    const KEY_PREFIX = null;

    public function __construct(
        ID $id
    ) {
        $this->id = $id;
    }

    /**
     * @var string
     */
    protected $id;

    public function getId(): ID
    {
        return $this->id;
    }
}
