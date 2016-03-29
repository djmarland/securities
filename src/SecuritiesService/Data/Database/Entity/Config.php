<?php
namespace SecuritiesService\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="config")})
 */
class Config extends Entity
{
    /** @ORM\Column(type="json_array") */
    private $settings = [];

    /** @ORM\Column(type="json_array") */
    private $features = [];

    /** Getters/Setters */
    public function getSettings()
    {
        return $this->settings;
    }

    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    public function getFeatures()
    {
        return $this->features;
    }

    public function setFeatures($features)
    {
        $this->features = $features;
    }
}
