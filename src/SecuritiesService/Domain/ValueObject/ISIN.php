<?php

namespace SecuritiesService\Domain\ValueObject;

class ISIN
{

    /**
     * @param $isin
     */
    public function __construct(
        string $isin
    ) {
        if (strlen($isin) != 12) {
            throw new \InvalidArgumentException('ISIN must be 12 characters long');
        }
        $this->isin = $isin;
    }

    /**
     * @var string
     */
    private $isin;

    public function getIsin(): string
    {
        return $this->isin;
    }

    public function __toString()
    {
        return (string) $this->getIsin();
    }
}
