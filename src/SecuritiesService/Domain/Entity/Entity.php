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

    /**
     * @param ID $id
     * @param DateTime $createdAt
     * @param DateTime $updatedAt
     */
    public function __construct(
        ID $id,
        DateTime $createdAt,
        DateTime $updatedAt
    ) {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @var string
     */
    protected $id;

    public function getId(): ID
    {
        return $this->id;
    }

    /**
     * @var \DateTime
     */
    protected $createdAt;

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }
}
