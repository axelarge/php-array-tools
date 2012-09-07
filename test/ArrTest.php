<?php
namespace Axelarge\ArrayTools\Test;

use Axelarge\ArrayTools\Arr;

require_once __DIR__.'/../vendor/autoload.php';

class ArrTest extends \PHPUnit_Framework_TestCase
{

    private function makeArr()
    {
        return new Arr(array(1, 2, 'key' => 'val', 123 => 456));
    }

    private function make5()
    {
        return new Arr(range(1, 5));
    }

    public function testCreationFromArray()
    {
        $arr = array(1, 2, 3, 'some' => 'value', 'key' => 'value two');
        $this->assertEquals($arr, Arr::wrap($arr)->toArray());
    }

    public function testCreationFromArgs()
    {
        $this->assertEquals(Arr::create(1, 2, 3, 'val', 'two')->toArray(), array(1, 2, 3, 'val', 'two'));
    }

    public function testEmptyConstructor()
    {
        $arr = new Arr();
        $this->assertEquals(array(), $arr->toArray());
    }

    public function testDup()
    {
        $arr = Arr::create(1, 2, 3);
        $dup = $arr->dup();
        $this->assertNotSame($arr, $dup);
        $this->assertEquals($arr->toArray(), $dup->toArray());
    }

    protected static function getDeeplyNestedArray()
    {
        return array(
            'a' => array(
                'a' => 42,
                'b' => array(
                    'a' => 'x',
                    'c' => array(1, 2, 3),
                ),
                'b.c' => 'unreachable',
            ),
        );
    }

    public static function getNestedProvider()
    {
        $nested = self::getDeeplyNestedArray();

        return array(
            array($nested, 'a.b.a', null, 'x', 'Retrieving a value works'),
            array($nested, 'a.b.a', 'd', 'x', 'Default value is only used when key is missing'),
            array($nested, 'a.b.c.0', 'd', 1, 'Retrieving keypaths containing numeric keys works'),
            array($nested, array('a', 'b', 'c', 0), 'd', 1, 'Retrieving keypaths given an array works'),
            array($nested, 'a.b.c.x', 'd', 'd', 'Default value is returned for non-existing key'),
            array($nested, array('foo', 'bar'), 'd', 'd', 'Default value is returned for non-existing array key'),
            array($nested, 'a.b.c', null, array(1, 2, 3), "Keys don't have to point to leaf elements"),
            array($nested, null, null, $nested, 'Passing null yields the whole array'),
        );
    }

    /**
     * @dataProvider getNestedProvider
     */
    public function testGetNested($arr, $key, $default, $expected, $message = null)
    {
        $this->assertEquals($expected, Arr::_getNested($arr, $key, $default), $message);
        $this->assertEquals($expected, Arr::w($arr)->getNested($key, $default), $message);
    }

    public function testLength()
    {
        $this->assertEquals(3, Arr::create(1, 2, 3)->length());
    }

    public function testKeys()
    {
        $this->assertEquals(array(0, 1, 'key', 123), $this->makeArr()->keys()->toArray());
    }

    public function testValues()
    {
        $this->assertEquals(array(1, 2, 'val', 456), $this->makeArr()->values()->toArray());
    }

    public function testTake()
    {
        $this->assertEquals(array(), $this->make5()->take(0)->toArray());
        $this->assertEquals(array(1, 2), $this->make5()->take(2)->toArray());
        $this->assertEquals(array(1, 2, 3, 4, 5), $this->make5()->take(5)->toArray());
        $this->assertEquals(array(4, 5), $this->make5()->take(-2)->toArray());
        $this->assertEquals(array(1, 2, 3, 4, 5), $this->make5()->take(-5)->toArray());
    }

    public function testDrop()
    {
        $this->assertEquals($this->make5(), $this->make5()->drop(0));
        $this->assertEquals(array(3, 4, 5), $this->make5()->drop(2)->toArray());
        $this->assertEquals(array(), $this->make5()->drop(5)->toArray());
        $this->assertEquals(array(1, 2, 3), $this->make5()->drop(-2)->toArray());
        $this->assertEquals(array(), $this->make5()->drop(-5)->toArray());
    }

    public function testJoin()
    {
        $arr = Arr::create(1, 2, 3);
        $this->assertEquals($arr->join(', '), '1, 2, 3');
    }

    public function testRepeat()
    {
        $arr = Arr::create(1, 2, 3);
        $this->assertEquals(array(1, 2, 3, 1, 2, 3, 1, 2, 3), $arr->dup()->repeat(3)->toArray());
        $this->assertEquals(array(), $arr->dup()->repeat(0)->toArray());
    }

    public function testOnly()
    {
        $arr = $this->makeArr()->only(Arr::create('key', 123));
        $this->assertEquals(array(123 => 456, 'key' => 'val'), $arr->toArray());
        $arr = $this->makeArr()->only(array(123, 1));
        $this->assertEquals(array(123 => 456, 1 => 2), $arr->toArray());
        $arr = $this->makeArr()->only(123, 1);
        $this->assertEquals(array(123 => 456, 1 => 2), $arr->toArray());
    }

    public function testExcept()
    {
        $arr = $this->makeArr()->except(Arr::create('key', 123));
        $this->assertEquals(array(1, 2), $arr->toArray());
        $arr = $this->makeArr()->except(array(123, 1));
        $this->assertEquals(array(1, 'key' => 'val'), $arr->toArray());
        $arr = $this->makeArr()->except(123, 0);
        $this->assertEquals(array(1 => 2, 'key' => 'val'), $arr->toArray());
    }

    public function testZip()
    {
        $a = Arr::create(1, 2, 3);
        $b = Arr::create(4, 5, 6);
        $c = array(
            array(1, 4),
            array(2, 5),
            array(3, 6),
        );
        $this->assertEquals($c, $a->zip($b)->toArray());
    }

    public function testZipWith()
    {
        $a = Arr::create(1, 2, 3);
        $b = Arr::create(4, 5, 6);
        $c = array(5, 7, 9);
        $this->assertEquals($c, $a->zipWith($b, function($a, $b) { return $a + $b; })->toArray());
    }

    // TODO preservekeys tests
    public function testChunk()
    {
        $this->assertEquals(
            array(
                Arr::create(1, 2),
                Arr::create(3, 4),
                Arr::create(5, 6),
            ),
            Arr::wrap(range(1, 6))->chunk(2)->toArray()
        );
    }

    public function testFilter()
    {
        $this->assertEquals(
            array(),
            Arr::wrap(range(3, 8))->filter(function ($x) { return $x < 3; })->toArray()
        );
        $this->assertEquals(
            array_values(array(3, 5, 7)),
            array_values(Arr::wrap(range(3, 8))->filter(function ($x) { return $x % 2; })->toArray())
        );
    }

    public function testMap()
    {
        $this->assertEquals(
            array(4, 9, 16),
            Arr::create(2, 3, 4)->map(function ($x) { return $x * $x; })->toArray()
        );
    }

    public function testPartition()
    {
        list($odd, $even) = $this->make5()->partition(function ($x) { return $x % 2; });
        /** @var $odd Arr */
        /** @var $even Arr */

        $this->assertEquals(array(1, 3, 5), $odd->values()->toArray());
        $this->assertEquals(array(2, 4), $even->values()->toArray());
    }

    public function testFirst()
    {
        $this->assertEquals('a', Arr::create('a', 'b', 'c', 'd')->first());
    }

    public function testLast()
    {
        $this->assertEquals('d', Arr::create('a', 'b', 'c', 'd')->last());
    }

    public function testIndexBy()
    {
        $this->assertEquals(
            array('baz' => 7, 'bar' => 8, 'foo' => 9),
            Arr::wrap(range(1, 9))->indexBy(function ($x) {
                if ($x % 3 === 0) return 'foo';
                if ($x % 2 === 0) return 'bar';
                return 'baz';
            })->toArray()
        );

        $this->assertEquals(
            array(
                1 => array('foo' => 1, 'bar' => 8),
                2 => array('foo' => 2, 'bar' => 10),
                3 => array('foo' => 3, 'bar' => 12),
            ),
            $this->getNested()->indexBy('foo')->toArray()
        );
    }

    public function testGroupBy()
    {
        $this->assertEquals(
            Arr::wrap(array(
                'foo' => Arr::create(3, 6, 9),
                'bar' => Arr::create(2, 4, 8),
                'baz' => Arr::create(1, 5, 7),
            )),
            Arr::wrap(range(1, 9))->groupBy(function ($x) {
                if ($x % 3 === 0) return 'foo';
                if ($x % 2 === 0) return 'bar';
                return 'baz';
            })
        );

        $this->assertEquals(
            Arr::wrap(array(
                1 => Arr::create(
                    array('foo' => 1, 'bar' => 2),
                    array('foo' => 1, 'bar' => 8)
                ),
                '2' => Arr::create(
                    array('foo' => 2, 'bar' => 4),
                    array('foo' => 2, 'bar' => 10)
                ),
                3 => Arr::create(
                    array('foo' => 3, 'bar' => 6),
                    array('foo' => 3, 'bar' => 12)
                ),
            )),
            $this->getNested()->groupBy('foo')
        );
    }

    public function testSample()
    {
        $this->assertEquals(3, Arr::wrap(array(1, 2, 3, 4, 5))->sample(3)->length(), "Selects multiple elements correctly");
        $this->assertEquals(1, Arr::wrap(array(1, 2, 3, 4, 5))->sample(1)->length(), "Selects an array of a single element when size is 1");
        $this->assertEquals(3, Arr::wrap(array(3, 3, 3, 3, 3))->sample(), "Selects a single element when size is omitted");
    }

    public function testStaticSample()
    {
        $this->assertEquals(3, count(Arr::_sample(array(1, 2, 3, 4, 5), 3)), "Selects multiple elements correctly");
        $this->assertEquals(1, count(Arr::_sample(array(1, 2, 3, 4, 5), 1)), "Selects an array of a single element when size is 1");
        $this->assertEquals(3, Arr::_sample(array(3, 3, 3, 3, 3)), "Selects a single element when size is omitted");
    }

    private function getNested()
    {
        return Arr::w(array(
            array('foo' => 1, 'bar' => 2),
            array('foo' => 2, 'bar' => 4),
            array('foo' => 3, 'bar' => 6),
            array('foo' => 1, 'bar' => 8),
            array('foo' => 2, 'bar' => 10),
            array('foo' => 3, 'bar' => 12),
        ));
    }


    public function testSum()
    {
        $this->assertEquals(15, $this->make5()->sum());
        $this->assertEquals(30, $this->make5()->sum(function ($x) { return $x * 2; }));
    }

    public function testMin()
    {
        $this->assertEquals(1, $this->make5()->min());
        $this->assertEquals(1, $this->make5()->reverse()->min());
    }

    public function testReverse()
    {
        $this->assertEquals(array(5, 4, 3, 2, 1), $this->make5()->reverse()->toArray());
    }

    public function testMinBy()
    {
        $id = function ($x) { return $x; };

        $this->assertEquals(
            array('foo' => 1, 'bar' => 2),
            $this->getNested()->minBy(function ($x) { return $x['bar']; })
        );
        $this->assertEquals(
            array('foo' => 3, 'bar' => 12),
            $this->getNested()->minBy(function ($x) { return -$x['bar']; })
        );
        $this->assertEquals('a', Arr::wrap(array('a', 'b', 'cde'))->minBy($id));
        $this->assertEquals(999, Arr::wrap(range(999, 1005))->minBy($id));
        $this->assertEquals(0, Arr::create(0)->minBy($id));
    }

    public function testMaxBy()
    {
        $id = function ($x) { return $x; };

        $this->assertEquals(
            array('foo' => 3, 'bar' => 12),
            $this->getNested()->maxBy(function ($x) { return $x['bar']; })
        );
        $this->assertEquals(
            array('foo' => 1, 'bar' => 2),
            $this->getNested()->maxBy(function ($x) { return -$x['bar']; })
        );

        $this->assertEquals('cde', Arr::wrap(array('a', 'b', 'cde'))->maxBy($id));
        $this->assertEquals(1005, Arr::wrap(range(999, 1005))->maxBy($id));
        $this->assertEquals(0, Arr::create(0)->maxBy($id));
    }

    public function testFlatten()
    {
        $this->assertEquals(
            array(3, 4, 5, 4, 6, 5),
            Arr::wrap(array(array(3, 4, 5), array(4, 6, 5)))->flatten()->toArray()
        );
    }

    public function testSort()
    {
        $sortable = Arr::w(array(
            5 => 1,
            4 => 2,
            3 => 3,
            2 => 4,
            1 => 5,
        ));

        $this->assertEquals(
            array(1, 2, 3, 4, 5),
            $sortable->dup()->sort()->toArray()
        );
        $this->assertEquals(
            array(5 => 1, 4 => 2, 3 => 3, 2 => 4, 1 => 5),
            $sortable->dup()->sort(true)->toArray()
        );
    }

    public function testSortBy()
    {
        $this->assertEquals(
            array(
                array('foo' => 3, 'bar' => 12),
                array('foo' => 2, 'bar' => 10),
                array('foo' => 1, 'bar' => 8),
                array('foo' => 3, 'bar' => 6),
                array('foo' => 2, 'bar' => 4),
                array('foo' => 1, 'bar' => 2),
            ),
            $this->getNested()->sortBy(function ($x) { return -$x['bar']; })->toArray()
        );
        $this->assertEquals(
            array(
                array('foo' => 1, 'bar' => 2),
                array('foo' => 1, 'bar' => 8),
                array('foo' => 2, 'bar' => 4),
                array('foo' => 2, 'bar' => 10),
                array('foo' => 3, 'bar' => 6),
                array('foo' => 3, 'bar' => 12),
            ),
            $this->getNested()->sortBy(function ($x) { return array($x['foo'], $x['bar']); })->toArray()
        );
        $this->assertEquals(
            array(
                array('foo' => 1, 'bar' => 2),
                array('foo' => 2, 'bar' => 4),
                array('foo' => 3, 'bar' => 6),
                array('foo' => 1, 'bar' => 8),
                array('foo' => 2, 'bar' => 10),
                array('foo' => 3, 'bar' => 12),
            ),
            $this->getNested()->sortBy('bar')->toArray()
        );
    }

    public function testFlatMap()
    {
        $this->assertEquals(
            array('foo', 'bar', 'baz'),
            Arr::_flatMap(array('foo', 'bar baz'), function ($s) { return explode(' ', $s); })
        );
    }

    public function testSliding()
    {
        $this->assertEquals(array(array(1, 2, 3), array(2, 3, 4), array(3, 4, 5)), Arr::_sliding(range(1, 5), 3)->toArray());
        $this->assertEquals(array(array(1, 2, 3, 4), array(4, 5)), Arr::_sliding(range(1, 5), 4, 3)->toArray());
//        $this->assertEquals(array(array(1, 2, 3), array(2, 3, 4), array(3, 4, 5)), Arr::wrap(range(1, 5))->sliding(3)->toArray());
//        $this->assertEquals(array(array(1, 2, 3, 4), array(4, 5)), Arr::wrap(range(1, 5))->sliding(4, 3)->toArray());
    }
}
