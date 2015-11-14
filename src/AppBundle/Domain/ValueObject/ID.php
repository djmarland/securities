<?php

namespace AppBundle\Domain\ValueObject;

/**
 * Class ID
 * For handling Identifiers
 */
class ID
{

    /**
     * @param $id
     */
    public function __construct(
        $id
    ) {
        if (!is_int($id)) {
            throw new \InvalidArgumentException('ID must be an Integer');
        }
        $this->id = $id;
    }

    /**
     * @var int
     */
    private $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function __toString()
    {
        return (string) $this->getId();
    }
}
