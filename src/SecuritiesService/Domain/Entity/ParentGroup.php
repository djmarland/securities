<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\Entity\Null\NullSector;
use SecuritiesService\Domain\Exception\DataNotSetException;
use SecuritiesService\Domain\ValueObject\UUID;

class ParentGroup extends Entity implements \JsonSerializable
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

    public function getSector()
    {
        if ($this->sector instanceof NullSector) {
            return null;
        }
        if (is_null($this->sector)) {
            throw new DataNotSetException('Tried to get sector data without requesting it up front');
        }
        return $this->sector;
    }

    public function jsonSerialize() {
        return (object) [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'sector' => $this->getSector(),
        ];
    }
}
