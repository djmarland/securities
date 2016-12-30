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
    public $id;

    /**
     * @ORM\Column(type="string")
     */
    public $uuid;

    /** @ORM\Column(type="datetime", nullable=false) */
    public $createdAt;

    /** @ORM\Column(type="datetime", nullable=false) */
    public $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->uuid = (string) $this->id;
    }

    /**
     * Set createdAt
     *
     * @ORM\PrePersist
     */
    public function onCreate()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    /**
     * Set updatedAt
     *
     * @ORM\PreUpdate
     */
    public function onUpdate()
    {
        $this->updatedAt = new DateTime();
    }
}
