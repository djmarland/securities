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
    /** @ORM\Column(type="string", length=255) */
    private $siteTitle = '';

    /** @ORM\Column(type="string", length=255) */
    private $siteTagline = '';

    /** @ORM\Column(type="json_array") */
    private $features = [];

    /** Getters/Setters */
    public function getSiteTitle()
    {
        return $this->siteTitle;
    }

    public function setSiteTitle($siteTitle)
    {
        $this->siteTitle = $siteTitle;
    }

    public function getSiteTagline()
    {
        return $this->siteTagline;
    }

    public function setSiteTagline($siteTagline)
    {
        $this->siteTagline = $siteTagline;
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
