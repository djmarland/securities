<?php
namespace Tests\AppBundle\Model;

use AppBundle\Model\Announcement;
use PHPUnit_Framework_TestCase;
use SecuritiesService\Domain\ValueObject\ISIN;

class AnnouncementTest extends PHPUnit_Framework_TestCase
{
    public function testCreatesSecurities()
    {
        $source = file_get_contents(__DIR__ . '/announcement-fixture.txt');
        $announcement = new Announcement($source);
        $securities = $announcement->getSecurities();

        $this->assertSame('2016-08-11', $announcement->getDate()->format('Y-m-d'));

        $this->assertSame(43, count($securities));

        $this->assertEquals(new ISIN('IE00B7ZQC614'), $securities[0]['isin']);
        $this->assertEquals(1.996281, $securities[0]['gbpAmount']);
    }
}
