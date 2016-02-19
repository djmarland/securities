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
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updated_at;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }


    /** Getters/Setters */
    public function getId()
    {
        return $this->id;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set createdAt
     *
     * @ORM\PrePersist
     */
    public function onCreate()
    {
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    /**
     * Set updatedAt
     *
     * @ORM\PreUpdate
     */
    public function onUpdate()
    {
        $this->updated_at = new \DateTime();
    }
}
