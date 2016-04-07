<?php

namespace SecuritiesService\Data;

use DateInterval;
use DateTimeImmutable;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\ValueObject\Bucket;
use SecuritiesService\Domain\ValueObject\BucketUndated;

class BucketProvider implements BucketProviderInterface
{
    const VALUE_DAY = 60*60*24;
    const VALUE_WEEK = self::VALUE_DAY * 7;
    const VALUE_MONTH = self::VALUE_DAY * 30;
    const VALUE_YEAR = self::VALUE_DAY * 365;

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
    const UNDATED = 'Undated';

    const UNDATED_BOUNDARY = [
        'key' => 'undated',
        'name' => self::UNDATED,
    ];

    const BUCKET_BOUNDARIES = [
        [
            'key' => '2w',
            'name' => self::FORTNIGHT,
            'lower' => 0,
            'upper' => self::VALUE_WEEK * 2,
        ],
        [
            'key' => '1m',
            'name' => self::MONTH,
            'lower' => self::VALUE_WEEK * 2,
            'upper' => self::VALUE_MONTH,
        ],
        [
            'key' => '3m',
            'name' => self::THREE_MONTH,
            'lower' => self::VALUE_MONTH,
            'upper' => self::VALUE_MONTH * 3,
        ],
        [
            'key' => '6m',
            'name' => self::SIX_MONTH,
            'lower' => self::VALUE_MONTH * 3,
            'upper' => self::VALUE_MONTH * 6,
        ],
        [
            'key' => '1y',
            'name' => self::ONE_YEAR,
            'lower' => self::VALUE_MONTH * 6,
            'upper' => self::VALUE_YEAR,
        ],
        [
            'key' => '2y',
            'name' => self::TWO_YEARS,
            'lower' => self::VALUE_YEAR,
            'upper' => self::VALUE_YEAR * 2,
        ],
        [
            'key' => '5y',
            'name' => self::FIVE_YEARS,
            'lower' => self::VALUE_YEAR * 2,
            'upper' => self::VALUE_YEAR * 5,
        ],
        [
            'key' => '10y',
            'name' => self::TEN_YEARS,
            'lower' => self::VALUE_YEAR * 5,
            'upper' => self::VALUE_YEAR * 10,
        ],
        [
            'key' => '15y',
            'name' => self::FIFTEEN_YEARS,
            'lower' => self::VALUE_YEAR * 10,
            'upper' => self::VALUE_YEAR * 15,
        ],
        [
            'key' => '15yplus',
            'name' => self::FIFTEEN_YEARS_PLUS,
            'lower' => self::VALUE_YEAR * 15,
            'upper' => null,
        ],
    ];

    private $currentTime;
    private $buckets;

    public function __construct(
        DateTimeImmutable $currentTime
    ) {
        $this->currentTime = $currentTime;
    }

    public function findAll(): array
    {
        return array_values($this->getBuckets());
    }

    public function findByKey($key): Bucket
    {
        $buckets = $this->getBuckets();
        if (array_key_exists($key, $buckets)) {
            return $buckets[$key];
        }
        throw new EntityNotFoundException('No such bucket with this key');
    }

    private function getBuckets(): array
    {
        if (!$this->buckets) {
            foreach (self::BUCKET_BOUNDARIES as $bucketData) {
                $this->buckets[$bucketData['key']] = $this->buildBucket($bucketData);
            }
            $this->buckets[self::UNDATED_BOUNDARY['key']] = new BucketUndated(
                $this->currentTime,
                self::UNDATED_BOUNDARY['name'],
                self::UNDATED_BOUNDARY['key']
            );
        }
        return $this->buckets;
    }

    private function buildBucket($bucketData)
    {
        $name = $bucketData['name'];
        $key = $bucketData['key'];

        $startTime = $this->currentTime;
        if ($bucketData['lower'] > 0) {
            $startTime = $startTime->add(
                new DateInterval('PT' . $bucketData['lower'] . 'S')
            );
        }

        $endTime = null;
        if ($bucketData['upper']) {
            $endTime = $this->currentTime->add(
                new DateInterval('PT' . $bucketData['upper'] . 'S')
            );
        }

        return new Bucket(
            $startTime,
            $name,
            $key,
            $endTime
        );
    }
}
