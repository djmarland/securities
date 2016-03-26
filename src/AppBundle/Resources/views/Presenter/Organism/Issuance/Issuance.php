<?php

namespace AppBundle\Presenter\Organism\Issuance;

use AppBundle\Presenter\Presenter;
use SecuritiesService\Domain\Entity\Entity;

abstract class Issuance extends Presenter
{
    protected $results;

    protected $currentTime;

    protected $options = [
        'cumulative' => false
    ];

    public function __construct(
        Entity $entity = null,
        array $results = [],
        array $options = []
    ) {
        parent::__construct($entity, $options);
        $this->results = $results;
        $this->currentTime = new \DateTimeImmutable(); // todo - use app time
    }

    protected function getMonths()
    {
        return [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];
    }
    
    protected function monthIsNotFuture($year, $month)
    {
        $date = new \DateTimeImmutable($year . '-' . $month . '-01');
        return ($date < $this->currentTime);
    }
}
