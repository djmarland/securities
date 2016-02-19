<?php

namespace SecuritiesService\Domain\Entity;

use SecuritiesService\Domain\ValueObject\UUID;
use DateTime;

class Currency extends Entity
{
    public function __construct(
        UUID $id,
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
