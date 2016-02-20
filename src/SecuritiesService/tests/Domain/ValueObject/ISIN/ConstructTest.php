<?php
namespace Tests\SecuritiesService\Domain\ValueObject\ISIN;

use SecuritiesService\Domain\ValueObject\ISIN;
use PHPUnit_Framework_TestCase;

class ConstructorTest extends PHPUnit_Framework_TestCase
{
    public function testSet()
    {
        $value = 'USG03762CE22';
        $isin = new ISIN($value);

        $this->assertSame($isin->getIsin(), $value);
        $this->assertSame((String) $isin, $value);
    }

    /**
     * @expectedException \SecuritiesService\Domain\Exception\ValidationException
     * @dataProvider invalidISINsProvider
     */
    public function testInvalidISIN($isinValue)
    {
        $isin = new ISIN($isinValue);
    }

    public function invalidISINsProvider()
    {
        return  [
            [''],
            ['12345678901'],
            ['1234567890123']
        ];
    }
}