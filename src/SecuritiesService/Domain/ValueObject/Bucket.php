<?php

namespace SecuritiesService\Domain\ValueObject;

use DateTimeImmutable;

class Bucket
{
    private $startDate;
    private $endDate;
    private $name;
    private $key;

    public function __construct(
        DateTimeImmutable $startDate,
        string $name,
        string $key,
        DateTimeImmutable $endDate = null
    ) {
        $this->startDate = $startDate;
        $this->name = $name;
        $this->key = $key;
        $this->endDate = $endDate;
    }


    public function getKey(): string
    {
        return $this->key;
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function __toString()
    {
        return (string) $this->getName();
    }

//    private function getOpenBucket()
//    {
//        foreach (Bucket::BUCKET_BOUNDARIES as $bucket) {
//            if (!$bucket['upper']) {
//                return $bucket;
//            }
//        }
//        return null;
//    }
//
//    private function calculateKey()
//    {
//        if (!$this->endDate) {
//            $openBucket = $this->getOpenBucket();
//            if ($openBucket) {
//                return $openBucket['key'];
//            }
//            return strtolower(self::UNKNOWN);
//        }
//
//        $diff = $this->endDate->getTimestamp() - $this->startDate->getTimestamp();
//        if ($diff < 0) {
//            return strtolower(self::COMPLETE);
//        }
//
//        foreach (Bucket::BUCKET_BOUNDARIES as $bucket) {
//            // the first one that is within the bounds wins
//            if ($diff >= $bucket['lower']) {
//                if (!$bucket['upper'] || $diff < $bucket['upper']) {
//                    return $bucket['key'];
//                }
//            }
//        }
//        return self::UNKNOWN;
//    }

//    private function calculateName()
//    {
//        if (!$this->endDate) {
//            $openBucket = $this->getOpenBucket();
//            if ($openBucket) {
//                return $openBucket['name'];
//            }
//            return self::UNKNOWN;
//        }
//
//        $diff = $this->endDate->getTimestamp() - $this->startDate->getTimestamp();
//        if ($diff < 0) {
//            return self::COMPLETE;
//        }
//
//        foreach (Bucket::BUCKET_BOUNDARIES as $bucket) {
//            // the first one that is within the bounds wins
//            if ($diff >= $bucket['lower']) {
//                if (!$bucket['upper'] || $diff < $bucket['upper']) {
//                    return $bucket['name'];
//                }
//            }
//        }
//        return self::UNKNOWN;
//    }
}
