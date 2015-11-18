<?php
namespace DatabaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="market_sector")})
 */
class MarketSector extends Entity
{

    /** @ORM\Column(type="string", length=255) */
    private $sector_code;

    /** Getters/Setters */
    public function getSectorCode()
    {
        return $this->sector_code;
    }

    public function setSectorCode($sector_code)
    {
        $this->sector_code = $sector_code;
    }
}