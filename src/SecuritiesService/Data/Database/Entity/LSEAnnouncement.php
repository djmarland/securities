<?php
namespace SecuritiesService\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="lse_announcements",indexes={@ORM\Index(name="lse_announcements_date_fetched_idx", columns={"date_fetched"})}))})
 */
class LSEAnnouncement extends Entity
{
    /** @ORM\Column(type="string", length=255, nullable=false) */
    private $title;

    /** @ORM\Column(type="string", length=255, nullable=false) */
    private $link;

    /** @ORM\Column(type="string", length=255, nullable=false) */
    private $description;

    /** @ORM\Column(type="string", length=255, nullable=false) */
    private $status;

    /** @ORM\Column(type="datetime", nullable=false) */
    private $dateFetched;

    /** Getters/Setters */
    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getDateFetched()
    {
        return $this->dateFetched;
    }

    public function setDateFetched($dateFetched)
    {
        $this->dateFetched = $dateFetched;
    }
}
