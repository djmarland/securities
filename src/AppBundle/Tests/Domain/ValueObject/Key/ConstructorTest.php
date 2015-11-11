<?php

namespace AppBundle\Tests\Domain\ValueObject\Key;

use AppBundle\Domain\ValueObject\Key;
use AppBundle\Domain\ValueObject\ID;
use InvalidArgumentException;

class ConstructorTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorSetKeyZeros()
    {
        $string = '000000';

        $key = new Key($string);
        $this->assertSame($string, $key->getKey());
        $this->assertSame('0', $key->getPrefix());
        $this->assertSame('000000', (string) $key);
    }

    public function testConstructorSetIDZeros()
    {
        $id = new ID(0);

        $key = new Key($id);
        $this->assertSame('000000', $key->getKey());
        $this->assertSame('0', $key->getPrefix());
        $this->assertSame('000000', (string) $key);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorInvalidInput()
    {
        new Key(1);
    }
}
