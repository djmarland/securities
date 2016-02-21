<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\ValueObject\UUID;

class ParentGroup extends Entity
{
    private $name;
    private $sector;

    public function __construct(
        UUID $id,
        string $name,
        Sector $sector = null
    ) {
        parent::__construct($id);

        $this->name = $name;
        $this->sector = $sector;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSector(): Sector
    {
        if (is_null($this->sector)) {
            // @todo, use a proper exception
            throw new \Exception('Tried to get sector data without requesting it up front');
        }
        return $this->sector;
    }
}
