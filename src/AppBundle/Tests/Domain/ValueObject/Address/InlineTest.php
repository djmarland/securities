<?php

namespace AppBundle\Tests\Domain\ValueObject\Address;

use AppBundle\Domain\ValueObject\Address;
use AppBundle\Domain\ValueObject\PostCode;

/**
 * Class InlineTest
 * @package AppNundle\Tests\Domain\ValueObject\Address
 * The toString method uses this, so test that too
 */
class InlineTest extends \PHPUnit_Framework_TestCase
{
    public function testBothEmpty()
    {
        $address = new Address();
        $this->assertNull($address->getInline());
        $this->assertEquals('', (string) $address);
    }

    public function testOnlyStreet()
    {
        $street = '95 Second Avenue';
        $address = new Address(
            $street
        );
        $this->assertEquals($street, $address->getInline());
        $this->assertEquals($street, (string) $address);
    }

    public function testOnlyPostcode()
    {
        $postcode = new PostCode('EX2 7BH');
        $address = new Address(
            null,
            $postcode
        );

        $this->assertEquals((string) $postcode, $address->getInline());
        $this->assertEquals((string) $postcode, (string) $address);
    }

    public function testBoth()
    {
        $postcode = new PostCode('EX2 7BH');
        $street = '95 Second Avenue';
        $expected = '95 Second Avenue, EX2 7BH';
        $address = new Address(
            $street,
            $postcode
        );
        $this->assertEquals($expected, $address->getInline());
        $this->assertEquals($expected, (string) $address);
    }
}
