<?php

namespace AppBundle\Domain\ValueObject;

use InvalidArgumentException;

class Key
{
    // inflate the ID to give customer IDs the illusion of authority (big company)
    // not done in the database to avoid needing bigint too quickly
    // this will be multiplied by the numerical value of the prefix prefix
    // inflation should be big enough to fill 3 digits (no B00BS)
    const ID_INFLATION_MULTIPLIER = 12000;

    const MIN_LENGTH = 6;

    const ALLOWED_CHARACTERS = '23456789BCDFGHJKLMNPQRSTVWXYZ';

    private $key;

    private $prefix;

    public function __construct(
        $key,
        $prefix = null
    ) {
        if ($key instanceof ID) {
            if (!$prefix) {
                $prefix = '0';
            }
            $key = $this->idToKey($key->getId(), $prefix);
        } elseif (is_string($key)) {
            $key = strtoupper($key);
            // validate
            $this->validate($key);
            $prefix = $key[0]; // grab the first character (string as array)
        } else {
            throw new \InvalidArgumentException('Constructor value was not an "ID" or a string');
        }
        $this->key = $key;

        $this->validatePrefix($prefix);
        $this->prefix = $prefix;
    }

    public function __toString()
    {
        return $this->getKey();
    }

    private function validatePrefix($prefix)
    {
        $prefix = strtoupper($prefix);
        if (!preg_match('/[A-Z0]{1}/', $prefix)) {
            throw new InvalidArgumentException('Prefix MUST be a single letter, or zero');
        }
        return true;
    }

    private function validate($key)
    {
        // must be at least the minimum length
        if (strlen($key) < static::MIN_LENGTH) {
            throw new InvalidArgumentException('Key too short. Must be 5 characters');
        }

        // strip the first character
        $key = substr($key, 1);

        // all zeroes is valid
        if (trim($key, '0') === '') {
            return true;
        }

        // strip any leading zeros (any amount of them are valid, but only leading)
        $key = ltrim($key, '0');

        // check the remaining characters are allowed
        $pattern = '/[^' . static::ALLOWED_CHARACTERS . ']/';
        if (preg_match($pattern, $key, $matches)) {
            throw new InvalidArgumentException(
                'Key contains invalid characters. After the prefix it can only be: ' .
                static::ALLOWED_CHARACTERS
            );
        }

        // all valid
        return true;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function getId()
    {
        $id = $this->keyToId();
        return new ID($id);
    }

    private function inflate($number, $prefix)
    {
        // when inflating. take the default inflation multiplier
        // multiply that by the numerical value of the prefix
        $code = ord($prefix);
        return $number + (static::ID_INFLATION_MULTIPLIER * $code);
    }

    private function deflate($number, $prefix)
    {
        $code = ord($prefix);
        return $number - (static::ID_INFLATION_MULTIPLIER * $code);
    }

    /**
     * @param int $id
     * @param string $prefix
     * @return string
     */
    protected function idToKey($id, $prefix)
    {
        if ($id == 0) {
            return $prefix . str_pad('', static::MIN_LENGTH - 1, '0');
        }

        $characters = self::ALLOWED_CHARACTERS;

        $base = strlen($characters);
        $id = $this->inflate($id, $prefix);
        $key = '';
        while ($id > 0) {
            // modulus cannot support high int values. must use float instead
            $newid = floor($id / $base);
            $key = $characters[(int) ($id - ($newid*$base))] . $key;
            $id = $newid;
        }
        return $prefix . str_pad($key, static::MIN_LENGTH - 1, '0', STR_PAD_LEFT);
    }

    /**
     * @return int
     */
    protected function keyToId()
    {
        $key = $this->getKey();
        $prefix = $this->getPrefix();

        // strip off the prefix
        $key = substr($key, 1);

        // remove any zeros
        $key = str_replace('0', '', $key);

        // if we just emptied it (all zeros) then
        // special null case (for dummy objects)
        if ($key === '') {
            return 0;
        }

        $base = strlen(self::ALLOWED_CHARACTERS);
        $key = array_reverse(str_split($key));
        $id = 0;
        $power = 0;
        foreach ($key as $letter) {
            $id = $id + (strpos(self::ALLOWED_CHARACTERS, $letter) * pow($base, $power));
            $power++;
        }
        return $this->deflate($id, $prefix);
    }
}
