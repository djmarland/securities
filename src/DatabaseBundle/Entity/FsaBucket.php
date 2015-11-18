<?php
namespace DatabaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="fsa_buckets")})
 */
class FsaBucket extends Entity
{
    /** @ORM\Column(type="float", length=5) */
    private $years_from;

    public function getYearsFrom()
    {
        return $this->years_from;
    }

    public function setYearsFrom($years_from)
    {
        $this->years_from = $years_from;
    }

    /** @ORM\Column(type="float", length=5, nullable=true) */
    private $years_to;

    public function getYearsTo()
    {
        return $this->years_to;
    }

    public function setYearsTo($years_to)
    {
        $this->years_to = $years_to;
    }
}