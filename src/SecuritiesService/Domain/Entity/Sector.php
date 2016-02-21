<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\Entity\Null\NullIndustry;
use SecuritiesService\Domain\Exception\DataNotSetException;
use SecuritiesService\Domain\ValueObject\UUID;

class Sector extends Entity
{
    private $name;
    private $industry;
    public function __construct(
        UUID $id,
        string $name,
        Industry $industry = null
    ) {
        parent::__construct($id);

        $this->name = $name;
        $this->industry = $industry;
    }
    public function getName(): string
    {
        return $this->name;
    }

    public function getIndustry()
    {
        if ($this->industry instanceof NullIndustry) {
            return null;
        }
        if (is_null($this->industry)) {
            throw new DataNotSetException('Tried to get industry data without requesting it up front');
        }
        return $this->industry;
    }
}
