<?php

namespace AppBundle\Tests\Domain\ValueObject\Key;

use AppBundle\Domain\ValueObject\Key;

class ValidationTest extends \PHPUnit_Framework_TestCase
{
    public function testValidZeros()
    {
        $string = '000000';
        $key = new Key($string);
        $this->assertSame($string, $key->getKey());

        $string = 'B00000';
        $key = new Key($string);
        $this->assertSame($string, $key->getKey());

        // many zeros
        $string = '000000000000';
        $key = new Key($string);
        $this->assertSame($string, $key->getKey());

        // leading zeros
        $string = 'B00002';
        $key = new Key($string);
        $this->assertSame($string, $key->getKey());
        $this->assertSame('B', $key->getPrefix());

    }

    /**
     * @dataProvider providerOfInvalidStrings
     */
    public function testIllegalStrings($string)
    {
        $this->setExpectedException('InvalidArgumentException');
        new Key($string);
    }

    public function providerOfInvalidStrings()
    {
        return [
            ['00000'],        // Too short, when zeros
            ['Z1234'],        // Too short
            [' 44444'],       // Invalid prefix (space)
            ['~44444'],       // Invalid prefix (punctuation)
            ['644444'],       // Invalid prefix (number)
            ['B44440'],       // Zeros after strip
            ['B11111'],       // Illegal Number (1)
            ['B4ACCC'],       // vowel (A)
            ['B4ECCC'],       // vowel (E)
            ['B4ICCC'],       // vowel (I)
            ['B4OCCC'],       // vowel (O)
            ['B4UCCC']        // vowel (U)
        ];

    }
}
