<?php
namespace Tests\SecuritiesService\Data;

use DateTimeImmutable;
use PHPUnit_Framework_TestCase;
use SecuritiesService\Data\BucketProvider;
use SecuritiesService\Domain\Exception\EntityNotFoundException;

class BucketProviderTest extends PHPUnit_Framework_TestCase
{
    public function testAllBuckets()
    {
        $date = new DateTimeImmutable('2016-02-02T12:00:00Z'); // 1454414400
        $provider = new BucketProvider($date);

        $buckets = $provider->findAll();
        $this->assertSame(11, count($buckets));

        $assertions = [
            [
                BucketProvider::FORTNIGHT,
                new DateTimeImmutable('2016-02-02T12:00:00Z'), // 1454414400
                new DateTimeImmutable('2016-16-02T12:00:00Z'), // 1455624000
            ],
            [
                BucketProvider::MONTH,
                new DateTimeImmutable('2016-16-02T12:00:00Z'), // 1455624000
                new DateTimeImmutable('2016-03-03T12:00:00Z'), // 1457006400
            ],
            [
                BucketProvider::THREE_MONTH,
                new DateTimeImmutable('2016-03-03T12:00:00Z'), // 1457006400
                new DateTimeImmutable('2016-06-02T12:00:00Z'), // 1462190400
            ],
            [
                BucketProvider::SIX_MONTH,
                new DateTimeImmutable('2016-06-02T12:00:00Z'), // 1462190400
                new DateTimeImmutable('2016-07-31T12:00:00Z'), // 1469966400
            ],
            [
                BucketProvider::ONE_YEAR,
                new DateTimeImmutable('2016-07-31T12:00:00Z'), // 1469966400
                new DateTimeImmutable('2017-02-01T12:00:00Z'), // 1485950400
            ],
            [
                BucketProvider::TWO_YEARS,
                new DateTimeImmutable('2017-02-01T12:00:00Z'), // 1485950400
                new DateTimeImmutable('2018-02-01T12:00:00Z'), // 1517486400
            ],
            [
                BucketProvider::FIVE_YEARS,
                new DateTimeImmutable('2018-02-01T12:00:00Z'), // 1517486400
                new DateTimeImmutable('2021-01-31T12:00:00Z'), // 1612094400
            ],
            [
                BucketProvider::TEN_YEARS,
                new DateTimeImmutable('2021-01-31T12:00:00Z'), // 1612094400
                new DateTimeImmutable('2026-01-30T12:00:00Z'), // 1769774400
            ],
            [
                BucketProvider::FIFTEEN_YEARS,
                new DateTimeImmutable('2026-01-30T12:00:00Z'), // 1769774400
                new DateTimeImmutable('2031-01-29T12:00:00Z'), // 1927454400
            ],
            [
                BucketProvider::FIFTEEN_YEARS_PLUS,
                new DateTimeImmutable('2031-01-29T12:00:00Z'), // 1927454400
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
        $this->assertEquals(
            new DateTimeImmutable('2021-01-31T12:00:00Z'),
            $bucket->getStartDate()
        );
        $this->assertEquals(
            new DateTimeImmutable('2026-01-30T12:00:00Z'),
            $bucket->getEndDate()
        );
    }

    /**
     * @expectedException EntityNotFoundException
     */
    public function testInvalidKey()
    {
        $date = new DateTimeImmutable('2016-02-02T12:00:00Z');
        $provider = new BucketProvider($date);

        $provider->findByKey('peanuts');
    }
}