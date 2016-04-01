<?php
namespace Tests\SecuritiesService\Data\;

use DateTimeImmutable;
use PHPUnit_Framework_TestCase;
use SecuritiesService\Data\BucketProvider;
use SecuritiesService\Domain\Exception\EntityNotFoundException;

class BucketProviderTest extends PHPUnit_Framework_TestCase
{
    public function testAllBuckets()
    {
        $date = new DateTimeImmutable('2016-02-02T12:00:00Z');
        $provider = new BucketProvider($date);

        $buckets = $provider->findAll();
        $this->assertSame(11, count($buckets));

        $assertions = [
            [
                BucketProvider::FORTNIGHT,
                new DateTimeImmutable('2016-02-02T12:00:00Z'),
                new DateTimeImmutable('2016-02-02T12:00:00Z'),
            ],
            [
                BucketProvider::MONTH,
                new DateTimeImmutable('2016-02-02T12:00:00Z'),
                new DateTimeImmutable('2016-02-02T12:00:00Z'),
            ],
            [
                BucketProvider::THREE_MONTH,
                new DateTimeImmutable('2016-02-02T12:00:00Z'),
                new DateTimeImmutable('2016-02-02T12:00:00Z'),
            ],
            [
                BucketProvider::SIX_MONTH,
                new DateTimeImmutable('2016-02-02T12:00:00Z'),
                new DateTimeImmutable('2016-02-02T12:00:00Z'),
            ],
            [
                BucketProvider::ONE_YEAR,
                new DateTimeImmutable('2016-02-02T12:00:00Z'),
                new DateTimeImmutable('2016-02-02T12:00:00Z'),
            ],
            [
                BucketProvider::TWO_YEARS,
                new DateTimeImmutable('2016-02-02T12:00:00Z'),
                new DateTimeImmutable('2016-02-02T12:00:00Z'),
            ],
            [
                BucketProvider::FIVE_YEARS,
                new DateTimeImmutable('2016-02-02T12:00:00Z'),
                new DateTimeImmutable('2016-02-02T12:00:00Z'),
            ],
            [
                BucketProvider::TEN_YEARS,
                new DateTimeImmutable('2016-02-02T12:00:00Z'),
                new DateTimeImmutable('2016-02-02T12:00:00Z'),
            ],
            [
                BucketProvider::FIFTEEN_YEARS,
                new DateTimeImmutable('2016-02-02T12:00:00Z'),
                new DateTimeImmutable('2016-02-02T12:00:00Z'),
            ],
            [
                BucketProvider::FIFTEEN_YEARS_PLUS,
                new DateTimeImmutable('2016-02-02T12:00:00Z'),
                null,
            ],
        ];

        foreach ($assertions as $key => $assertion) {
            $this->assertInstanceOf('Bucket', $buckets[$key]);
            $this->assertSame($assertion[0], $buckets[$key]->getName());
            $this->assertEquals($assertion[1], $buckets[$key]->getStartDate());
            $this->assertEquals($assertion[2], $buckets[$key]->getEndDate());
        }

        $this->assertInstanceOf('BucketUndated', $buckets[10]);
        $this->assertSame(BucketProvider::UNDATED, $buckets[10]->getName());
    }

    public function testFindByKey()
    {
        $date = new DateTimeImmutable('2016-02-02T12:00:00Z');
        $provider = new BucketProvider($date);

        $bucket = $provider->findByKey('10y');

        $this->assertSame(BucketProvider::TEN_YEARS, $bucket->getName());
    }

    /**
     * @expectedException EntityNotFoundException
     */
    public function testInvalidKey()
    {
        $date = new DateTimeImmutable('2016-02-02T12:00:00Z');
        $provider = new BucketProvider($date);

        $bucket = $provider->findByKey('peanuts');
    }
}