<?php

namespace AppBundle\Domain\Entity;

use AppBundle\Domain\ValueObject\ID;
use DateTime;

class Company extends Entity
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
        string $name
    ) {
        parent::__construct(
            $id,
            $createdAt,
            $updatedAt
        );

        $this->name = $name;
    }

    /**
     * @var string
     */
    private $name;

    public function getName(): string
    {
        return $this->name;
    }
}
