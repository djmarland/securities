<?php

namespace SecuritiesService\Domain\ValueObject;

use Djmarland\ISIN\Exception\InvalidISINException;
use SecuritiesService\Domain\Exception\ValidationException;

class ISIN
{
    private $isin;

    public function __construct(
        string $isin
    ) {
        try {
            \Djmarland\ISIN\ISIN::validate($isin);
        } catch (InvalidISINException $e) {
            throw new ValidationException($e->getMessage());
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
