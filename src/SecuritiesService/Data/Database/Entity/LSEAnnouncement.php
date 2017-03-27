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
    public $title;
    /** @ORM\Column(type="string", length=255, nullable=false) */
    public $link;
    /** @ORM\Column(type="string", length=255, nullable=false) */
    public $description;
    /** @ORM\Column(type="integer", nullable=false) */
    public $status;
    /** @ORM\Column(type="datetime", nullable=false) */
    public $dateFetched;
}
