<?php
namespace DatabaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="settings")
 */
class Settings extends Entity
{
    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $active_status = 0;

    /** @ORM\Column(type="string", length=255, nullable=false) */
    private $application_name;


    /** Getters/Setters */
    public function getActiveStatus()
    {
        return $this->active_status;
    }

    public function getApplicationName()
    {
        return $this->application_name;
    }

    public function setApplicationName($name)
    {
        $this->application_name = $name;
    }
}
