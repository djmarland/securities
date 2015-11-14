<?php
namespace DatabaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity
* @ORM\HasLifecycleCallbacks()
* @ORM\Table(name="securities",indexes={@ORM\Index(name="isin_idx", columns={"isin"})})
*/
class Security extends Entity
{
    /** @ORM\Column(type="string", length=255) */
    private $name;

    /** @ORM\Column(type="string", length=12) */
    private $isin;

    /** Getters/Setters */
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getIsin(): string
    {
        return $this->isin;
    }

    public function setIsin(string $isin)
    {
        $this->isin = $isin;
    }
}
