<?php

namespace SecuritiesService\Domain\ValueObject;

use SecuritiesService\Domain\Exception\ValidationException;

class ISIN
{
    private $isin;

    public function __construct(
        string $isin
    ) {
        if (strlen($isin) != 12) {
            throw new ValidationException('ISIN must be 12 characters long');
        }
        $this->isin = $isin;
    }

    public function getIsin(): string
    {
        return $this->isin;
    }

    public function __toString()
    {
        return (string) $this->getIsin();
    }
}
