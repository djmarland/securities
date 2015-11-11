<?php
namespace AppBundle\Tests\Presenter\MasterPresenter;

use AppBundle\Presenter\MasterPresenter;

class ValuesTest extends \PHPUnit_Framework_TestCase
{
    private $presenter;

    public function setUp()
    {
        $this->presenter = new MasterPresenter();
    }

    public function testUnset()
    {
        $this->setExpectedException('App\Domain\Exception\DataNotSetException');
        $this->presenter->fromView('dog');
    }

    public function testValue()
    {
        $key = 'dog';
        $value = 'Destiny';
        $this->presenter->toView($key, $value);
        $this->assertSame($value, $this->presenter->fromView($key));
    }

    public function testObjectValueIsNotTouched()
    {
        $key = 'show';
        $value = (object) [
            'title' => 'Mongrels'
        ];
        $this->presenter->toView($key, $value);
        $this->assertSame($value, $this->presenter->fromView($key));
    }

    public function testSetGetData()
    {
        // add a value
        $this->presenter->toView('fox', 'Nelson');

        // overwrite the value
        $this->presenter->toView('fox', 'Vince');

        // add a non feed value
        $this->presenter->toView('cat', 'Marion');

        // add a non feed value
        $this->presenter->toView('pigeon', 'Kali', false);

        // get data back (alphabetical)
        $this->assertSame(
            $this->presenter->getData(),
            [
                'cat'       => 'Marion',
                'fox'       => 'Vince',
                'pigeon'    => 'Kali'
            ]
        );

        // get feed data back (alphabetical)
        $this->assertEquals(
            $this->presenter->getFeedData(),
            (object) [
                'cat' => 'Marion',
                'fox' => 'Vince'
            ]
        );
    }
}
