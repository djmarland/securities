<?php

namespace AppBundle\Tests\Domain\ValueObject\ID;

use AppBundle\Domain\ValueObject\ID;
use InvalidArgumentException;

class ConstructorTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorSetID()
    {
        $idValue = 1234;

        $id = new ID($idValue);
        $this->assertSame($idValue, $id->getId());
        $this->assertSame('1234', (string) $id);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorSetNotInt()
    {
        $idValue = 'Bob';
        new ID($idValue);
    }
}
