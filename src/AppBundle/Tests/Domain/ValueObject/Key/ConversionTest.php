<?php

namespace AppBundle\Tests\Domain\ValueObject\Key;

use AppBundle\Domain\ValueObject\ID;
use AppBundle\Domain\ValueObject\Key;

class ConversionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider providerOfKeys
     */
    public function testKeyToId($key, $expectedId)
    {
        $expectedId = new ID($expectedId);
        $key = new Key($key);
        $this->assertEquals($expectedId, $key->getId());
    }

    /**
     * @dataProvider providerOfIds
     */
    public function testIdToKey($id, $prefix, $expectedKey)
    {
        $id = new ID($id);
        $key = new Key($id, $prefix);
        $this->assertSame($expectedKey, $key->getKey());
    }

    public function providerOfIds()
    {
        return [
            [0,     null,   '000000'],
            [0,     '0',    '000000'],
            [0,     'B',    'B00000'],
            [1,     'B',    'B35HRF'], // B = 66. (66 * 12000) + 1 = 792001
            [2,     'B',    'B35HRG'], // B = 66. (66 * 12000) + 1 = 792002
            [2,     'C',    'C35Z28'], // C = 67. (67 * 12000) + 1 = 804002
            [2,     'Z',    'Z3KB7H'], // C = 67. (90 * 12000) + 1 = 1080002
            [100000,'Z',    'Z3PF4P'], // Z = 90. (100000 * 12000) + 1 = 1200000001
            [99999999999,'Z',    'Z7T5H3FFG'], // crazy big number
        ];
    }

    public function providerOfKeys()
    {
        // take the IDs, and flip them
        // this ensures conversion is tested both ways
        $keys = [];
        $ids = $this->providerOfIds();
        foreach ($ids as $id) {
            $keys[] = [end($id), reset($id)];
        }
        return $keys;
    }
}
