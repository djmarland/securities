<?php

namespace AppBundle\Domain\Entity;

use AppBundle\Domain\ValueObject\ID;
use AppBundle\Domain\ValueObject\ISIN;
use DateTime;

class Currency extends Entity
{

    /**
     * @param ID $id
     * @param $createdAt
     * @param $updatedAt
     * @param $code
     */
    public function __construct(
        ID $id,
        DateTime $createdAt,
        DateTime $updatedAt,
        string $code
    ) {
        parent::__construct(
            $id,
            $createdAt,
            $updatedAt
        );

        $this->code = $code;
    }

    /**
     * @var string
     */
    private $code;

    public function getCode(): string
    {
        return $this->code;
    }
}
