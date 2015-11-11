<?php

namespace AppBundle\Domain\Entity;

use AppBundle\Domain\ValueObject\ID;
use AppBundle\Domain\ValueObject\IDUnset;
use AppBundle\Domain\ValueObject\Key;
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

    /**
     * @return string
     */
    public function getId()
    {
        if ($this->id instanceof IDUnset) {
            return null; // this means it's a new object
        }
        return $this->id;
    }

    public function getIdValue()
    {
        $id = $this->getId();
        if ($id) {
            return $id->getId();
        }
        return null;
    }

    /**
     * @param ID $id
     */
    public function setId(ID $id)
    {
        // may only be set if it wasn't before
        if (!($this->id instanceof IDUnset)) {
            throw new \InvalidArgumentException('Tried to set an ID when one was already set');
        }
        $this->id = $id;
    }

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    protected $key;

    /**
     * @return Key
     */
    public function getKey()
    {
        if (!$this->key) {
            $this->key = new Key($this->id, static::KEY_PREFIX);
        }
        return $this->key;
    }

    /**
     * @var \DatabaseBundle\Entity\Entity
     */
    protected $ormEntity;

    /**
     * @param \DatabaseBundle\Entity\Entity $ormEntity
     */
    public function setOrmEntity($ormEntity)
    {
        $this->ormEntity = $ormEntity;
    }

    /**
     * @return \DatabaseBundle\Entity\Entity$ormEntity
     */
    public function getOrmEntity()
    {
        return $this->ormEntity;
    }

    protected $changed = false;

    protected function updated()
    {
        $this->changed = true;
        $this->updatedAt = new DateTime();
    }

    public function hasChanged()
    {
        return $this->changed;
    }
}
