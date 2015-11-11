<?php

namespace AppBundle\Tests\Domain\Entity\Customer;

use AppBundle\Domain\Entity\Customer;
use AppBundle\Domain\ValueObject\ID;
use AppBundle\Domain\ValueObject\Address;
use AppBundle\Domain\ValueObject\PostCode;

class ConstructorTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorDefaults()
    {
        $id = new ID(123);
        $name = 'Lucy Luck';
        $date = new \DateTime();
        $customer = new Customer(
            $id,
            $date,
            $date,
            $name
        );

        $this->assertSame($id, $customer->getId());
        $this->assertSame($name, $customer->getName());
        $this->assertNull($customer->getAddress());
    }

    public function testConstructorFull()
    {
        $street = '95 Second Avenue';
        $postcode = new PostCode('EX2 7BH');
        $address = new Address(
            $street,
            $postcode
        );
        $date = new \DateTime();

        $id = new ID(123);
        $name = 'Lucy Luck';
        $customer = new Customer(
            $id,
            $date,
            $date,
            $name,
            $address
        );

        $this->assertSame($id, $customer->getId());
        $this->assertSame($name, $customer->getName());
        $this->assertSame($address, $customer->getAddress());
    }
}
