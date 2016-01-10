<?php

namespace SecuritiesService\Domain\ValueObject;

use DateTime;
use DateInterval;
use DateTimeImmutable;
use InvalidArgumentException;

class Bucket
{

    const UNDATED = 'Undated';
    const OVERNIGHT = 'Overnight';
    const FORTNIGHT = 'Fortnight';
    const SIX_MONTHS = '6 Months';
    const ONE_YEAR = '1 Year';
    const ONE_THREE_YEARS = '1 - 3 Years';
    const THREE_FIVE_YEARS = '3 - 5 Years';
    const FIVE_TEN_YEARS = '5 - 10 Years';
    const TEN_YEARS_PLUS = '10 Years+';

    // the value is the upper bound of the bucket in seconds
    const BUCKETS = [
        0 => self::UNDATED,
        (60*60*24)  => self::OVERNIGHT,
        (60*60*24*14) => self::FORTNIGHT,
        (60*60*24*7*26) => self::SIX_MONTHS,
        (60*60*24*365) => self::ONE_YEAR,
        (60*60*24*365*3) => self::ONE_THREE_YEARS,
        (60*60*24*365*5) => self::THREE_FIVE_YEARS,
        (60*60*24*365*10) => self::FIVE_TEN_YEARS,
        (60*60*24*365*100) => self::TEN_YEARS_PLUS
    ];

    private $startDate;

    private $endDate;

    private $diff = 0;

    private $bucketName;

    public static function getAllBuckets(
        DateTime $startDate
    ) {
        $names = array_values(self::BUCKETS);
        return array_map(function($name) use ($startDate) {
            return new self($startDate, self::endDateOfBucket($startDate, $name));
        }, $names);
    }

    public static function endDateOfBucket(
        DateTime $startDate,
        string $bucketName
    ) {
        $buckets = array_flip(self::BUCKETS);
        if (!isset($buckets[$bucketName])) {
            throw new InvalidArgumentException('Invalid bucket');
        }
        $seconds = $buckets[$bucketName];

        if (!($startDate instanceof DateTimeImmutable)) {
            $startDate = DateTimeImmutable::createFromMutable($startDate);
        }

        $endDate = $startDate->add(new DateInterval('PT'.$seconds.'S'));
        // @todo - use immutable everywhere so this isn't needed
        return new DateTime($endDate->format('c'));
    }

    public function __construct(
        DateTime $startDate,
        DateTime $endDate = null
    ) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->calculate();
    }

    private function calculate()
    {
        if ($this->endDate) {
            $this->diff = $this->endDate->getTimestamp() - $this->startDate->getTimestamp();
        }

        $buckets = self::BUCKETS;

        foreach ($buckets as $upper => $bucket) {
            // the first one that is greater than or equal to the diff wins
            if ($upper >= $this->diff) {
                $this->bucketName = $bucket;
                break;
            }
        }
        return reset($buckets);
    }

    public function getEndDate()
    {
        return $this->getEndDate();
    }

    public function getName(): string
    {
        return $this->bucketName;
    }

    public function __toString()
    {
        return (string) $this->bucketName;
    }
}
