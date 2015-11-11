<?php
namespace AppBundle\Security;

use AppBundle\Domain\ValueObject\Password;
use AppBundle\Domain\ValueObject\PasswordDigest;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class Encoder implements PasswordEncoderInterface
{

    public function encodePassword($password, $salt)
    {
        $password = new Password($password);
        return $password->getDigest();
    }

    public function isPasswordValid($encoded, $raw, $salt)
    {
        $digest = new PasswordDigest($encoded);
        return $digest->matches($raw);
    }
}