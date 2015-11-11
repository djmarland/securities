<?php

namespace AppBundle\Tests\Domain\ValueObject\Address;

use AppBundle\Domain\ValueObject\Address;
use AppBundle\Domain\ValueObject\PostCode;

class ConstructorTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorSetNone()
    {
        $address = new Address();
        $this->assertNull($address->getStreet());
        $this->assertNull($address->getPostcode());
    }

    public function testConstructorSetBoth()
    {
        $street = '95 Second Avenue';
        $postcode = new PostCode('EX2 7BH');
        $address = new Address(
            $street,
            $postcode
        );
        $this->assertSame($street, $address->getStreet());
        $this->assertSame($postcode, $address->getPostcode());
    }
}
