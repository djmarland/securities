<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\ValueObject\ID;
use SecuritiesService\Domain\ValueObject\ISIN;
use DateTime;

class Currency extends Entity
{
    public function __construct(
        ID $id,
        string $code
    ) {
        parent::__construct($id);

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
