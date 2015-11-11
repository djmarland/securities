<?php

namespace AppBundle\Domain\Entity;

use AppBundle\Domain\ValueObject\Address;
use AppBundle\Domain\ValueObject\ID;

/**
 * Class User
 * For describe users of the system
 */
class Customer extends Entity
{
    const KEY_PREFIX = 'C';

    /**
     * @param ID $id
     * @param $createdAt
     * @param $updatedAt
     * @param $name
     * @param Address $address
     */
    public function __construct(
        ID $id,
        $createdAt,
        $updatedAt,
        $name,
        Address $address = null
    ) {
        parent::__construct(
            $id,
            $createdAt,
            $updatedAt
        );

        $this->name = $name;
        $this->address = $address;
    }

    /**
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @var string
     */
    private $address;

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }
}
