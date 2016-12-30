<?php
namespace SecuritiesService\Domain\ValueObject;

class CurvePoints implements \IteratorAggregate
{
    private $points;

    public function __construct(
        array $points
    ) {
        $this->points = $points;
    }

    public function __toString()
    {
        return json_encode($this->points);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->points);
    }
}
