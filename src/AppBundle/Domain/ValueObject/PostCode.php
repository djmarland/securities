<?php

namespace AppBundle\Domain\ValueObject;

/**
 * Class Postcode
 * For handling and validating postcodes
 */
class PostCode
{

    public function __construct(
        $postcode
    ) {

        // @todo - validate
        $this->postcode = $postcode;
    }

    /**
     * @var string
     */
    private $postcode;

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->postcode;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }
}
