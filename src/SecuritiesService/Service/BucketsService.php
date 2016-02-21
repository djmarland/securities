<?php

namespace SecuritiesService\Service;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use SecuritiesService\Domain\ValueObject\Bucket;
use SecuritiesService\Domain\ValueObject\BucketUndated;

class BucketsService extends Service
{
    public function getAll(
        DateTime $startDate
    ): array {
        return $this->generateBuckets($startDate);
    }

    public function findByKey(
        string $key,
        DateTime $startDate
    ): ServiceResultInterface {
        $buckets = $this->generateBuckets($startDate);
        foreach ($buckets as $bucket) {
            if ($bucket->getKey() == $key) {
                return new ServiceResult(
                    $bucket
                );
            }
        }
        return new ServiceResultEmpty();
    }

    public function getBucketFromDates(
        DateTime $startDate,
        DateTime $endDate = null
    ) {
        if ($endDate) {
            return new Bucket(
                $startDate,
                $endDate
            );
        }
        return new BucketUndated($startDate);
    }

    private function generateBuckets(
        DateTime $startDate
    ): array {
        $buckets = array_map(function ($bucket) use ($startDate) {
            $endDate = null;
            $seconds = $bucket['upper'] ?? null;
            if ($seconds) {
                $diffDate = $startDate;
                if (!($diffDate instanceof DateTimeImmutable)) {
                    $diffDate = DateTimeImmutable::createFromMutable($diffDate);
                }
                $endDate = $diffDate->add(new DateInterval('PT' . $seconds . 'S'));
                // @todo - use immutable everywhere so this isn't needed
                $endDate = new DateTime($endDate->format('c'));
            }

            $calcStartDate = $startDate;
            if (!($calcStartDate instanceof DateTimeImmutable)) {
                $calcStartDate = DateTimeImmutable::createFromMutable($calcStartDate);
            }
            $startDate = $calcStartDate->add(new DateInterval('PT' . abs($bucket['lower']) . 'S'));
            $startDate = new DateTime($startDate->format('c'));
            return new Bucket($startDate, $endDate);
        }, Bucket::BUCKET_BOUNDARIES);

        $buckets[] = new BucketUndated($startDate);
        return $buckets;
    }
}
