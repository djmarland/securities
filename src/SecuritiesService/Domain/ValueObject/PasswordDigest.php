<?php

namespace SecuritiesService\Domain\ValueObject;

/**
 * Class Address
 * For handling addresses
 */
class PasswordDigest
{

    /**
     * @var string
     */
    private $digest;

    public function __construct(
        $digest
    ) {
        $this->digest = $digest;
    }

    public function matches($match)
    {
        return password_verify($match, $this->digest);
    }

    public function __toString()
    {
        return $this->digest;
    }
}
