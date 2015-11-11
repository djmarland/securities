<?php

namespace AppBundle\Tests\Domain\ValueObject\Password;

use AppBundle\Domain\ValueObject\Password;

class ConstructorTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $word = 'ZXCVBN'; // 6 characters
        $password = new Password($word);
        $this->assertTrue(is_string($password->getDigest()));
        $this->assertTrue(password_verify($word, (string) $password));
    }

    /**
     * @dataProvider providerOfInvalidPasswords
     * @param $password
     */
    public function testIllegalPasswords($password)
    {
        $this->setExpectedException('AppBundle\Domain\Exception\ValidationException');
        new Password($password);
    }

    public function providerOfInvalidPasswords()
    {
        return [
            ['1'],
            ['12'],
            ['123'],
            ['1234'],
            ['12345'],
            ['123456'],
            ['password']
        ];
    }
}
