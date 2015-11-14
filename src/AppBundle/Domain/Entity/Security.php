<?php

namespace AppBundle\Domain\Entity;

use AppBundle\Domain\ValueObject\ID;
use AppBundle\Domain\ValueObject\ISIN;
use DateTime;

class Security extends Entity
{

    /**
     * @param ID $id
     * @param $createdAt
     * @param $updatedAt
     * @param ISIN $isin
     * @param $name
     */
    public function __construct(
        ID $id,
        DateTime $createdAt,
        DateTime $updatedAt,
        ISIN $isin,
        string $name
    ) {
        parent::__construct(
            $id,
            $createdAt,
            $updatedAt
        );

        $this->name = $name;
        $this->isin = $isin;
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
     * @var string
     */
    private $isin;

    public function getIsin(): string
    {
        return $this->isin;
    }
}
