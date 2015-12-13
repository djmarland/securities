<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\ValueObject\ID;
use DateTime;

class Fsa04748 extends Entity
{

    /**
     * @param ID $id
     * @param $createdAt
     * @param $updatedAt
     * @param $name
     */
    public function __construct(
        ID $id,
        DateTime $createdAt,
        DateTime $updatedAt,
        int $line,
        string $name
    ) {
        parent::__construct(
            $id,
            $createdAt,
            $updatedAt
        );

        $this->name = $name;
        $this->line = $line;
    }

    /**
     * @var string
     */
    private $name;

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @var int
     */
    private $line;

    public function getLine(): int
    {
        return $this->line;
    }
}
