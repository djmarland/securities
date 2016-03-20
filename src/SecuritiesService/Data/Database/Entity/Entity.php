<?php
namespace SecuritiesService\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Ramsey\Uuid\Uuid;

abstract class Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid_binary")
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $uuid;

    /** @ORM\Column(type="datetime", nullable=false) */
    protected $createdAt;

    /* @ORM\Column(type="datetime", nullable=false) */
    protected $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->uuid = (string) $this->id;
    }

    /** Getters/Setters */
    public function getId()
    {
        return $this->id;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set createdAt
     *
     * @ORM\PrePersist
     */
    public function onCreate()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Set updatedAt
     *
     * @ORM\PreUpdate
     */
    public function onUpdate()
    {
        $this->updatedAt = new \DateTime();
    }
}
