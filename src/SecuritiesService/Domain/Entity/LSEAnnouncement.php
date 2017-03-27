<?php

namespace SecuritiesService\Domain\Entity;

use DateTimeImmutable;
use SecuritiesService\Domain\Entity\Enum\AnnouncementStatus;
use SecuritiesService\Domain\ValueObject\UUID;

class LSEAnnouncement extends Entity
{
    private $title;
    private $description;
    private $link;
    private $dateFetched;
    private $status;

    public function __construct(
        UUID $id,
        string $title,
        string $description,
        string $link,
        DateTimeImmutable $dateFetched,
        string $status
    ) {
        parent::__construct($id);

        $this->title = $title;
        $this->description = $description;
        $this->link = $link;
        $this->dateFetched = $dateFetched;
        $this->status = $status;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getShortLink(): string
    {
        $parts = explode('/', $this->link);
        return 'http://.../' . end($parts);
    }

    public function getRouteParams(): array
    {
        return ['lse_id' => (string) $this->id];
    }

    public function getRowClass(): string
    {
        if ($this->getStatus() == AnnouncementStatus::DONE) {
            return 'message--ok';
        }
        if ($this->getStatus() == AnnouncementStatus::ERROR) {
            return 'message--error';
        }
        return '';
    }

    public function getDateFetched(): DateTimeImmutable
    {
        return $this->dateFetched;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getStatusString(): string
    {
        if ($this->status == AnnouncementStatus::DONE) {
            return 'Processed';
        }
        if ($this->status == AnnouncementStatus::ERROR) {
            return 'Error';
        }
        if ($this->status == AnnouncementStatus::NEW) {
            return 'New';
        }
        if ($this->status == AnnouncementStatus::LOW) {
            return 'Low Priority';
        }
        return 'Unknown';
    }

    public function isNew(): bool
    {
        return $this->status == AnnouncementStatus::NEW;
    }

    public function isDone(): bool
    {
        return $this->status == AnnouncementStatus::DONE;
    }

    public function isError(): bool
    {
        return $this->status == AnnouncementStatus::ERROR;
    }
}
