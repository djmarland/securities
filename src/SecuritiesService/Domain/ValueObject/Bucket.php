<?php

namespace SecuritiesService\Domain\ValueObject;

use DateTime;
use DateInterval;
use DateTimeImmutable;
use InvalidArgumentException;

class Bucket
{

    const VALUE_DAY = 60*60*24;
    const VALUE_WEEK = self::VALUE_DAY * 7;
    const VALUE_MONTH = self::VALUE_DAY * 30;
    const VALUE_YEAR = self::VALUE_DAY * 365;

    const UNKNOWN = 'Unknown';
    const COMPLETE = 'Complete';
    const UNDATED = 'Undated';

    const FORTNIGHT = '< 2 weeks';
    const MONTH = '2 weeks - 1 month';
    const THREE_MONTH = '1-3 months';
    const SIX_MONTH = '3-6 months';
    const ONE_YEAR = '6 months - 1 year';
    const TWO_YEARS = '1-2 years';
    const FIVE_YEARS = '2-5 years';
    const TEN_YEARS = '5-10 years';
    const FIFTEEN_YEARS = '10-15 years';
    const FIFTEEN_YEARS_PLUS = '15+ years';

    const BUCKET_BOUNDARIES = [
        [
            'key' => '2w',
            'name' => self::FORTNIGHT,
            'lower' => -1,
            'upper' => self::VALUE_WEEK * 2
        ],
        [
            'key' => '1m',
            'name' => self::MONTH,
            'lower' => self::VALUE_WEEK * 2,
            'upper' => self::VALUE_MONTH
        ],
        [
            'key' => '3m',
            'name' => self::THREE_MONTH,
            'lower' => self::VALUE_MONTH,
            'upper' => self::VALUE_MONTH * 3
        ],
        [
            'key' => '6m',
            'name' => self::SIX_MONTH,
            'lower' => self::VALUE_MONTH * 3,
            'upper' => self::VALUE_MONTH * 6
        ],
        [
            'key' => '1y',
            'name' => self::ONE_YEAR,
            'lower' => self::VALUE_MONTH * 6,
            'upper' => self::VALUE_YEAR
        ],
        [
            'key' => '2y',
            'name' => self::TWO_YEARS,
            'lower' => self::VALUE_YEAR,
            'upper' => self::VALUE_YEAR * 2
        ],
        [
            'key' => '5y',
            'name' => self::FIVE_YEARS,
            'lower' => self::VALUE_YEAR * 2,
            'upper' => self::VALUE_YEAR * 5
        ],
        [
            'key' => '10y',
            'name' => self::TEN_YEARS,
            'lower' => self::VALUE_YEAR * 5,
            'upper' => self::VALUE_YEAR * 10
        ],
        [
            'key' => '15y',
            'name' => self::FIFTEEN_YEARS,
            'lower' => self::VALUE_YEAR * 10,
            'upper' => self::VALUE_YEAR * 15
        ],
        [
            'key' => '15yplus',
            'name' => self::FIFTEEN_YEARS_PLUS,
            'lower' => self::VALUE_YEAR * 15,
            'upper' => null
        ]
    ];

    private $startDate;

    private $endDate;

    private $name;

    public function __construct(
        DateTime $startDate,
        DateTime $endDate = null
    ) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    private $key;

    public function getKey(): string
    {
        if (!$this->key) {
            $this->key = $this->calculateKey();
        }
        return $this->key;

    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function getName(): string
    {
        if (!$this->name) {
            $this->name = $this->calculateName();
        }
        return $this->name;
    }

    private function getOpenBucket()
    {
        foreach (Bucket::BUCKET_BOUNDARIES  as $bucket) {
            if (!$bucket['upper']) {
                return $bucket;
            }
        }
        return null;
    }

    private function calculateKey()
    {
        if (!$this->endDate) {
            $openBucket = $this->getOpenBucket();
            if ($openBucket) {
                return $openBucket['key'];
            }
            return strtolower(self::UNKNOWN);
        }

        $diff = $this->endDate->getTimestamp() - $this->startDate->getTimestamp();
        if ($diff < 0) {
            return strtolower(self::COMPLETE);
        }

        foreach (Bucket::BUCKET_BOUNDARIES  as $bucket) {
            // the first one that is within the bounds wins
            if ($diff >= $bucket['lower']) {
                if (!$bucket['upper'] || $diff < $bucket['upper']) {
                    return $bucket['key'];
                }
            }
        }
        return self::UNKNOWN;
    }

    private function calculateName()
    {
        if (!$this->endDate) {
            $openBucket = $this->getOpenBucket();
            if ($openBucket) {
                return $openBucket['name'];
            }
            return self::UNKNOWN;
        }

        $diff = $this->endDate->getTimestamp() - $this->startDate->getTimestamp();
        if ($diff < 0) {
            return self::COMPLETE;
        }

        foreach (Bucket::BUCKET_BOUNDARIES  as $bucket) {
            // the first one that is within the bounds wins
            if ($diff >= $bucket['lower']) {
                if (!$bucket['upper'] || $diff < $bucket['upper']) {
                    return $bucket['name'];
                }
            }
        }
        return self::UNKNOWN;
    }

    public function __toString()
    {
        return (string) $this->getName();
    }
}
