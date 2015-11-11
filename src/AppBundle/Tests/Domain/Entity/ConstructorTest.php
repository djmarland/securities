<?php
namespace AppBundle\Tests\Domain\Entity;

use AppBundle\Domain\ValueObject\ID;
use AppBundle\Domain\ValueObject\IDUnset;
use AppBundle\Domain\ValueObject\Key;

class ConstructorTest extends \PHPUnit_Framework_TestCase
{
    const MODEL = 'AppBundle\Domain\Entity\Entity';

    public function testGetId()
    {
        $id = new ID(1);
        $mockEntity = $this->getMockBuilder(self::MODEL)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $date = new \DateTime();

        $mockEntity->__construct($id, $date, $date);

        $this->assertTrue($mockEntity->getId() instanceof ID);
        $this->assertSame($id, $mockEntity->getId());
        $this->assertSame(1, $mockEntity->getIdValue());
    }

    public function testNullId()
    {
        $id = new IDUnset();
        $mockEntity = $this->getMockBuilder(self::MODEL)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $date = new \DateTime();

        $mockEntity->__construct($id, $date, $date);

        $this->assertNull($mockEntity->getId());
        $this->assertNull($mockEntity->getIdValue());
    }

    public function testSetIDWhenNull()
    {
        $id = new IDUnset;
        $mockEntity = $this->getMockBuilder(self::MODEL)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $date = new \DateTime();

        $mockEntity->__construct($id, $date, $date);

        $this->assertNull($mockEntity->getId());

        $newId = new ID(2);
        $mockEntity->setId($newId);
        $this->assertSame($newId, $mockEntity->getId());
    }

    public function testSetIDWhenAlreadySet()
    {
        $this->setExpectedException('InvalidArgumentException');

        $id = new ID(1);
        $mockEntity = $this->getMockBuilder(self::MODEL)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $date = new \DateTime();

        $mockEntity->__construct($id, $date, $date);
        $newId = new ID(2);
        $mockEntity->setId($newId);
    }

    public function testGetKey()
    {
        $id = new ID(0);
        $date = new \DateTime();

        $mockEntity = $this->getMockBuilder(self::MODEL)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $mockEntity->__construct($id, $date, $date);

        // full test of the key is in the key test
        $key = $mockEntity->getKey();
        $this->assertTrue($key instanceof Key);
        $this->assertSame('000000', (string) $key);
    }
}
