<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\ValueObject\UUID;

class Sector extends Entity
{
    public function __construct(
        UUID $id,
        string $name,
        Industry $industry = null
    ) {
        parent::__construct($id);

        $this->name = $name;
        $this->industry = $industry;
    }

    /**
     * @var string
     */
    private $name;

    public function getName(): string
    {
        return $this->name;
    }

    private $industry;

    public function getIndustry(): Industry
    {
        if (is_null($this->industry)) {
            // @todo, use a proper exception
            throw new \Exception('Tried to get industry data without requesting it up front');
        }
        return $this->industry;
    }
}
