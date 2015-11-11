<?php

namespace AppBundle\Domain\ValueObject;

/**
 * Class Address
 * For handling addresses
 */
class Address
{

    public function __construct(
        $street = null,
        PostCode $postcode = null
    ) {

        // @todo - cleanse the street

        $this->street = $street;
        $this->postcode = $postcode;
    }

    /**
     * @var string
     */
    private $street;

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @var string
     */
    private $postcode;

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @return string
     */
    public function getInline()
    {
        $parts = array();
        if (!empty($this->getStreet())) {
            $parts[] = $this->getStreet();
        }
        if (!empty($this->getPostcode())) {
            $parts[] = $this->getPostcode();
        }
        if (!empty($parts)) {
            return implode(', ', $parts);
        }
        return null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $address = $this->getInline();
        if ($address) {
            return $address;
        }
        return '';
    }
}
