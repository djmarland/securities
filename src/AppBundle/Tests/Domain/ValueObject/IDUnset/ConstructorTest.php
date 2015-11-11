<?php

namespace AppBundle\Tests\Domain\ValueObject\IDUnset;

use AppBundle\Domain\ValueObject\IDUnset;

class ConstructorTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorSetID()
    {
        $id = new IDUnset();
        // getID actually came from the parent
        $this->assertNull($id->getId());
    }
}
