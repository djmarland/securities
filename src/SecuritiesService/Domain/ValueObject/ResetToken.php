<?php

namespace SecuritiesService\Domain\ValueObject;

class ResetToken
{
    private $username;

    private $password;

    private $fullToken;

    /**
     * @var Password
     */
    private $passwordDigest;

    public function __construct(
        string $token = null
    ) {
        if ($token) {
            $this->fullToken = $token;
            $this->handleToken();
        } else {
            $this->generateNewToken();
        }
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getToken(): string
    {
        return $this->fullToken;
    }

    public function getDigest(): PasswordDigest
    {
        return $this->passwordDigest->getDigest();
    }

    public function __toString(): string
    {
        return $this->fullToken;
    }

    private function generateNewToken()
    {
        // 40 characters each
        $this->username = sha1(mt_rand());
        $this->password = sha1(mt_rand());
        $this->fullToken = $this->username . $this->password;
        $this->generateDigest();
    }

    private function handleToken()
    {
        // split the full token in half
        $parts = str_split($this->fullToken, strlen($this->fullToken)/2);
        $username = $parts[0];
        $password = $parts[1];
        $this->username = $username;
        $this->password = $password;
        $this->generateDigest();
    }

    private function generateDigest()
    {
        $this->passwordDigest = new Password($this->password);
    }
}
