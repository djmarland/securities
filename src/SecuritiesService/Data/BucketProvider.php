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

    const LESS_THAN_ONE_YEAR = '< 1 year';
    const ONE_TO_FIVE_YEARS = '1 - 5 years';
    const FIVE_TO_TWELVE_YEARS = '5 - 12 years';
    const TWELVE_YEARS_PLUS = '12+ years';
    const UNDATED = 'Undated';

    const UNDATED_BOUNDARY = [
        'key' => 'undated',
        'name' => self::UNDATED,
    ];

    const BUCKET_BOUNDARIES = [
        [
            'key' => '0-1y',
            'name' => self::LESS_THAN_ONE_YEAR,
            'lower' => 0,
            'upper' => self::VALUE_YEAR,
        ],
        [
            'key' => '1-5y',
            'name' => self::ONE_TO_FIVE_YEARS,
            'lower' => self::VALUE_YEAR,
            'upper' => self::VALUE_YEAR * 5,
        ],
        [
            'key' => '5-12y',
            'name' => self::FIVE_TO_TWELVE_YEARS,
            'lower' => self::VALUE_YEAR * 5,
            'upper' => self::VALUE_YEAR * 12,
        ],
        [
            'key' => '12yplus',
            'name' => self::TWELVE_YEARS_PLUS,
            'lower' => self::VALUE_YEAR * 12,
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
