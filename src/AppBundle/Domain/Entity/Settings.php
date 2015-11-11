<?php

namespace AppBundle\Domain\Entity;

/**
 * Class User
 * For describe users of the system
 */
class Settings extends Entity
{

    const STATUS_INACTIVE = 0;

    const STATUS_SUSPENDED = -1;

    const STATUS_ACTIVE = 1;

    /**
     * @param $id
     * @param $createdAt
     * @param $updatedAt
     * @param $activeStatus
     * @param $appName
     */
    public function __construct(
        $id,
        $createdAt,
        $updatedAt,
        $activeStatus,
        $appName
    ) {
        parent::__construct(
            $id,
            $createdAt,
            $updatedAt
        );

        $this->activeStatus = $activeStatus;
        $this->applicationName = $appName;
    }

    /**
     * @var integer
     */
    private $activeStatus;

    /**
     * @return integer
     */
    public function getActiveStatus()
    {
        return $this->activeStatus;
    }

    public function isActive()
    {
        return ($this->activeStatus == self::STATUS_ACTIVE);
    }

    public function isSuspended()
    {
        return ($this->activeStatus == self::STATUS_SUSPENDED);
    }

    /**
     * @var string
     */
    private $applicationName;

    /**
     * @return string
     */
    public function getApplicationName()
    {
        return $this->applicationName;
    }

    public function setApplicationName($name)
    {
        if ($name != $this->applicationName) {
            // @todo - validate not empty
            $this->applicationName = $name;
            $this->updated();
        }
    }
}
