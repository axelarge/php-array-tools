<?php
namespace Axelarge\ArrayTools\Test;

use Axelarge\ArrayTools\Arr;
use Axelarge\ArrayTools\WrappedArray;

require_once __DIR__.'/../vendor/autoload.php';

class ArrTest extends \PHPUnit_Framework_TestCase
{

    private function makeArr()
    {
        return new WrappedArray(array(1, 2, 'key' => 'val', 123 => 456));
    }

    private function make5()
    {
        return new WrappedArray(range(1, 5));
    }

    public function testCreationFromArray()
    {
        $arr = array(1, 2, 3, 'some' => 'value', 'key' => 'value two');
        $this->assertEquals($arr, Arr::wrap($arr)->toArray());
    }

    public function testEmptyConstructor()
    {
        $arr = new WrappedArray();
        $this->assertEquals(array(), $arr->toArray());
    }

    public function testDup()
    {
        $arr = WrappedArray::wrap(array(1, 2, 3));
        $dup = $arr->dup();
        $this->assertNotSame($arr, $dup, 'Returns a different object');
        $this->assertEquals($arr->toArray(), $dup->toArray(), 'Contents are the same');
        $dup->push(4);
        $this->assertNotEquals($arr->toArray(), $dup->toArray(), 'Modifying copy does not change the original');
    }


    // ----- Traversal -----

    public function testEach()
    {
        $s = '';
        Arr::range('a', 'c')->each(function ($v, $k) use (&$s) { $s .= "$v:$k "; });
        $this->assertEquals('a:0 b:1 c:2 ', $s);
    }

    public function testEachWithIndex()
    {
        $s = '';
        Arr::eachWithIndex(range('a', 'c'), function ($v, $k) use (&$s) { $s .= "$v:$k "; });
        $this->assertEquals('a:0 b:1 c:2 ', $s, 'Static version');

        $s = '';
        Arr::range('a', 'c')->eachWithIndex(function ($v, $k) use (&$s) { $s .= "$v:$k "; });
        $this->assertEquals('a:0 b:1 c:2 ', $s);
    }


    // ----- Single element access -----

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
            array($nested, 'a.a.a',  'd', 'd', 'If intermediate key is present but not an array, returns default'),
        );
    }

    /**
     * @dataProvider getNestedProvider
     */
    public function testGetNested($arr, $key, $default, $expected, $message = null)
    {
        $this->assertEquals($expected, Arr::getNested($arr, $key, $default), $message);
        $this->assertEquals($expected, Arr::wrap($arr)->getNested($key, $default), $message);
    }

    public function testGetOrElse()
    {
        $arr = Arr::wrap(array(1, 2, 'foo' => 3));
        $this->assertEquals(2, $arr->getOrElse(1), 'Works with numeric index');
        $this->assertEquals(3, $arr->getOrElse('foo'), 'Works with text index');
        $this->assertEquals('default', $arr->getOrElse('bar', 'default'), 'Returns default value for non-existing key');
    }

    public function testStaticGetOrElse()
    {
        $arr = array(1, 2, 'foo' => 3);
        $this->assertEquals(2, Arr::getOrElse($arr, 1), 'Works with numeric index');
        $this->assertEquals(3, Arr::getOrElse($arr, 'foo'), 'Works with text index');
        $this->assertEquals('default', Arr::getOrElse($arr, 'bar', 'default'), 'Returns default value for non-existing key');
    }

    public function testStaticGetOrPut()
    {
        $arr = array(1, 2, 'foo' => 3, 'bar' => null);
        $this->assertEquals(3, Arr::getOrPut($arr, 'foo', 'bar'), 'Retrieves existing value');
        $this->assertEquals(3, $arr['foo'], 'Retrieval of existing value does not modify it');
        $this->assertEquals('baz', Arr::getOrPut($arr, 'key', 'baz'), 'Returns default value if key missing');
        $this->assertEquals('baz', $arr['key'], 'Inserts back default value if key missing');
        $this->assertEquals(null, Arr::getOrPut($arr, 'bar', 'x'), 'Can retrieve null value');
    }

    public function testGetOrPut()
    {
        $arr = Arr::wrap(array(1, 2, 'foo' => 3, 'bar' => null));
        $this->assertEquals(3, $arr->getOrPut('foo', 'bar'), 'Retrieves existing value');
        $this->assertEquals(3, $arr['foo'], 'Retrieval of existing value does not modify it');
        $this->assertEquals('baz', $arr->getOrPut('key', 'baz'), 'Returns default value if key missing');
        $this->assertEquals('baz', $arr['key'], 'Inserts back default value if key missing');
        $this->assertEquals(null, $arr->getOrPut('bar', 'x'), 'Can retrieve null value');
    }

    public function testStaticGetAndDelete()
    {
        $arr = array(1, 2, 'foo' => 3, 'bar' => null);
        $this->assertEquals(3, Arr::getAndDelete($arr, 'foo'), 'Retrieves value');
        $this->assertFalse(isset($arr['foo']), 'Deletes value after retrieval');
        $this->assertEquals(null, Arr::getAndDelete($arr, 'bar', 'x'), 'Can retrieve null value');
        $this->assertFalse(isset($arr['bar']), 'Deleted null value after retrieval');
    }

    public function testGetAndDelete()
    {
        $arr = Arr::wrap(array(1, 2, 'foo' => 3, 'bar' => null));
        $this->assertEquals(3, $arr->getAndDelete('foo'), 'Retrieves value');
        $this->assertFalse(isset($arr['foo']), 'Deletes value after retrieval');
        $this->assertEquals(null, $arr->getAndDelete('bar', 'x'), 'Can retrieve null value');
        $this->assertFalse(isset($arr['bar']), 'Deleted null value after retrieval');
    }

    public function testFirst()
    {
        $this->assertEquals(1, Arr::range(1, 5)->first(), 'Retrieves the first element');
    }

    public function testLast()
    {
        $this->assertEquals(5, Arr::range(1,5)->last(), 'Retrieves the last element');
    }


    // ----- Slicing -----

    public function testTake()
    {
        $this->assertEquals(array(), $this->make5()->take(0)->toArray(), 'take(0) returns empty array');
        $this->assertEquals(array(1, 2), $this->make5()->take(2)->toArray(), 'Taking from the left');
        $this->assertEquals(array(1, 2, 3, 4, 5), $this->make5()->take(5)->toArray(), 'Taking all elements');
        $this->assertEquals(array(4, 5), $this->make5()->take(-2)->toArray(), 'Taking from the right');
        $this->assertEquals(array(1, 2, 3, 4, 5), $this->make5()->take(-5)->toArray(), 'Taking all elements from the right');
    }

    public function testDrop()
    {
        $this->assertEquals($this->make5(), $this->make5()->drop(0), 'drop(0) returns all elements');
        $this->assertEquals(array(3, 4, 5), $this->make5()->drop(2)->toArray(), 'Dropping from the left');
        $this->assertEquals(array(), $this->make5()->drop(5)->toArray(), 'Dropping all');
        $this->assertEquals(array(1, 2, 3), $this->make5()->drop(-2)->toArray(), 'Dropping from the right');
        $this->assertEquals(array(), $this->make5()->drop(-5)->toArray(), 'Dropping all');
    }

    public function testStaticTakeWhile()
    {
        $this->assertEquals(array(1, 2), Arr::takeWhile(array(1, 2, 3, 1, 2, 3), function ($x) { return $x < 3; }));
    }

    public function testTakeWhile()
    {
        $arr = Arr::wrap(array(1, 2, 3, 1, 2, 3));
        $this->assertEquals(array(1, 2), $arr->takeWhile(function ($x) { return $x < 3; })->toArray());
    }

    public function testStaticDropWhile()
    {
        $this->assertEquals(array(3, 1, 2, 3), Arr::dropWhile(array(1, 2, 3, 1, 2, 3), function ($x) { return $x < 3; }));
    }

    public function testDropWhile()
    {
        $arr = Arr::wrap(array(1, 2, 3, 1, 2, 3));
        $this->assertEquals(array(3, 1, 2, 3), $arr->dropWhile(function ($x) { return $x < 3; })->toArray());
    }

    public function testSlice()
    {
        $this->assertEquals(array(2, 3), Arr::range(1, 5)->slice(1, 2)->toArray(), 'Takes a slice');
        $this->assertEquals(Arr::range(2, 5), Arr::range(1, 5)->slice(1), 'Slices to the end if no length given');
        $this->assertEquals(Arr::range(2, 4), Arr::range(1, 5)->slice(1, -1), 'Support negative length');
        $this->assertEquals(
            array('bar' => 2, 'baz' => 3),
            Arr::wrap(array('foo' => 1, 'bar' => 2, 'baz' => 3, 'quux' => 4))->slice(1, 2, true)->toArray(),
            'Can preserve hash keys'
        );
    }

    public function testSplice()
    {
        $arr = Arr::wrap(array(1, 2, 'foo' => 3, 123 => 4));
        $this->assertEquals(
            array(1, 2, 'there', 'fellas', 4),
            $arr->splice(2, 1, array('hey' => 'there', 'fellas'))->toArray()
        );
    }

    public function testReverse()
    {
        $this->assertEquals(array(5, 4, 3, 2, 1), $this->make5()->reverse()->toArray());
    }

    public function testRepeat()
    {
        $arr = Arr::range(1, 3);
        $this->assertEquals(array(1, 2, 3, 1, 2, 3, 1, 2, 3), $arr->repeat(3)->toArray());
        $this->assertEquals(array(), $arr->repeat(0)->toArray(), 'Repeating 0 times yields empty array');
        $this->assertEquals(array(1, 2, 3, 1, 2, 3, 1, 2, 3), Arr::repeat(range(1, 3), 3));
        $this->assertEquals(array(), Arr::repeat(range(1, 3), 0));
    }


    // ----- Finding -----

    public function testHasKey()
    {
        $arr = Arr::wrap(array(1, 2, 'foo' => 3, 123 => 4));
        $this->assertTrue($arr->hasKey(1), 'with numeric key');
        $this->assertTrue($arr->hasKey(123), 'with non-sequential numeric key');
        $this->assertTrue($arr->hasKey('foo'), 'with string key');
        $this->assertFalse($arr->hasKey('bar'), 'non-existing key');
    }

    public function testHasValue()
    {
        $arr = Arr::wrap(array(1, 2, 0, 'foo' => 'bar', 123 => 4));
        $this->assertTrue($arr->hasValue(2), 'numeric value');
        $this->assertTrue($arr->hasValue('bar'), 'string value');
        $this->assertFalse($arr->hasValue(''), 'strict by default');
        $this->assertTrue($arr->hasValue('', false), 'non-strict on request');
    }

    public function testFinders()
    {
        $p = function ($x) { return $x % 3 == 0; };
        $match = array(1, 6, 2, 3, 5);
        $noMatch = array(1, 2, 4);

        $this->assertEquals(6, Arr::find($match, $p));
        $this->assertEquals(-1, Arr::find($noMatch, $p, -1));
        $this->assertEquals(3, Arr::findLast($match, $p));
        $this->assertEquals(-1, Arr::findLast($noMatch, $p, -1));
        $this->assertEquals(1, Arr::findKey($match, $p));
        $this->assertNull(Arr::findKey($noMatch, $p));
        $this->assertEquals(3, Arr::findLastKey($match, $p));
        $this->assertNull(Arr::findLastKey($noMatch, $p));

        $match1 = Arr::wrap($match);
        $noMatch1 = Arr::wrap($noMatch);
        $this->assertEquals(6, $match1->find($p));
        $this->assertEquals(-1, $noMatch1->find($p, -1));
        $this->assertEquals(3, $match1->findLast($p));
        $this->assertEquals(-1, $noMatch1->findLast($p, -1));
        $this->assertEquals(1, $match1->findKey($p));
        $this->assertNull($noMatch1->findKey($p));
        $this->assertEquals(3, $match1->findLastKey($p));
        $this->assertNull($noMatch1->findLastKey($p));
    }

    public function testIndexOf()
    {
        $arr = array(0, 1, 2, '', 1, 2);
        $wrap = Arr::wrap($arr);

        $this->assertEquals(1, $wrap->indexOf(1));
        $this->assertEquals(3, $wrap->indexOf(''), 'strict by default');
        $this->assertEquals(0, $wrap->indexOf('', false), 'non-strict by request');
        $this->assertNull($wrap->indexOf(' '), 'element missing');

        $this->assertEquals(4, Arr::lastIndexOf($arr, 1));
        $this->assertEquals(0, Arr::lastIndexOf($arr, 0), 'strict by default');
        $this->assertEquals(3, Arr::lastIndexOf($arr, 0, false), 'non-strict by request');
        $this->assertNull(Arr::lastIndexOf($arr, ' '), 'element missing');

        $this->assertEquals(4, $wrap->lastIndexOf(1));
        $this->assertEquals(0, $wrap->lastIndexOf(0), 'strict by default');
        $this->assertEquals(3, $wrap->lastIndexOf(0, false), 'non-strict by request');
        $this->assertNull($wrap->lastIndexOf(' '), 'element missing');
    }

    public function testKeys()
    {
        $this->assertEquals(array(0, 1, 'key', 123), $this->makeArr()->keys()->toArray());
    }

    public function testValues()
    {
        $this->assertEquals(array(1, 2, 'val', 456), $this->makeArr()->values()->toArray());
    }
    
    public function testOnly()
    {
        $arr = array(1, 2, 'key' => 'val', 123 => 456);
        $wrap = Arr::wrap($arr);
        $expected = array('key' => 'val', 123 => 456);

        $this->assertEquals($expected, Arr::only($arr, array('key', 123)));
        $this->assertEquals($expected, $wrap->only(Arr::wrap(array('key', 123)))->toArray());
        $this->assertEquals($expected, $wrap->only(array('key', 123))->toArray());
        $this->assertEquals($expected, $wrap->only('key', 123)->toArray());
    }

    public function testExcept()
    {
        $arr = array(1, 2, 'key' => 'val', 123 => 456);
        $wrap = Arr::wrap($arr);

        $this->assertEquals(array(1 => 2, 'key' => 'val'), Arr::except($arr, array(0, 123)));
        $this->assertEquals(array(1, 2), $wrap->except(Arr::wrap(array('key', 123)))->toArray());
        $this->assertEquals(array(1, 'key' => 'val'), $wrap->except(array(123, 1))->toArray());
        $this->assertEquals(array(1 => 2, 'key' => 'val'), $wrap->except(123, 0)->toArray());
    }

    private function getNested()
    {
        return Arr::wrap(array(
            array('foo' => 1, 'bar' => 2),
            array('foo' => 2, 'bar' => 4),
            array('foo' => 3, 'bar' => 6),
            array('foo' => 1, 'bar' => 8),
            array('foo' => 2, 'bar' => 10),
            array('foo' => 3, 'bar' => 12),
        ));
    }

    private function getFriends()
    {
        return array(
            array('name' => 'Alice', 'age' => 33),
            array('name' => 'Bob', 'age' => 29),
            array('name' => 'Harry', 'age' => 33),
        );
    }

    public function testIndexBy()
    {
        $byName = array(
            'Alice' => array('name' => 'Alice', 'age' => 33),
            'Bob'   => array('name' => 'Bob', 'age' => 29),
            'Harry' => array('name' => 'Harry', 'age' => 33),
        );
        $byAge = array(
            33 => array('name' => 'Harry', 'age' => 33),
            29 => array('name' => 'Bob', 'age' => 29),
        );

        $this->assertEquals($byName, Arr::indexBy($this->getFriends(), 'name'));
        $this->assertEquals($byAge, Arr::indexBy($this->getFriends(), 'age'));
        $this->assertEquals($byName, Arr::wrap($this->getFriends())->indexBy('name')->toArray());
        $this->assertEquals($byAge, Arr::wrap($this->getFriends())->indexBy('age')->toArray());
    }

    public function testGroupBy()
    {
        $expected = array(
            33  => array(array('name' => 'Alice', 'age' => 33), array('name' => 'Harry', 'age' => 33)),
            29  => array(array('name' => 'Bob', 'age' => 29)),
        );

        $this->assertEquals($expected, Arr::groupBy($this->getFriends(), 'age'));
        $this->assertEquals($expected, Arr::wrap($this->getFriends())->groupBy('age')->toArray(true));
    }

    public function testMerge()
    {
        $a = Arr::wrap(array('foo' => 1, 'bar' => 2));
        $b = Arr::wrap(array('bar' => 3, 'baz' => 4));

        $this->assertEquals(array('foo' => 1, 'bar' => 3, 'baz' => 4), $a->merge($b)->toArray(), 'Forward merge');
        $this->assertEquals(2, $a['bar'], 'Merging does not change original');
        $this->assertEquals(array('foo' => 1, 'bar' => 2, 'baz' => 4), $a->reverseMerge($b)->toArray(), 'Reverse merge');
        $this->assertEquals(2, $a['bar'], 'Reverse merging does not change original');
    }

    public function testCombine()
    {
        $this->assertEquals(
            array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4),
            Arr::range('a', 'd')->combine(range(1, 4))->toArray()
        );

        $this->assertEquals(
            array('a' => 3, 'b' => 2),
            Arr::wrap(array('a', 'b', 'a'))->combine(range(1, 3))->toArray()
        );
    }

    public function testFlip()
    {
        $this->assertEquals(
            array(1 => 'a', 2 => 'd', 3 => 'c'),
            Arr::wrap(array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 2))->flip()->toArray()
        );
    }


    // ----- Mutation methods -----

    public function testMutators()
    {
        $arr = Arr::range(1, 3);
        $arr->push(4);
        $this->assertEquals(Arr::range(1, 4), $arr, 'Push');
        $arr->unshift(0);
        $this->assertEquals(Arr::range(0, 4), $arr, 'Unshift');
        $this->assertEquals(4, $arr->pop(), 'Pop returns last value');
        $this->assertEquals(Arr::range(0, 3), $arr, 'Pop alters callee');
        $this->assertEquals(0, $arr->shift(), 'Shift returns first value');
        $this->assertEquals(Arr::range(1, 3), $arr, 'Shift alters callee');
    }


    // ----- Set operations -----

    public function testIntersection()
    {
        $a = Arr::range(1, 5);
        $b = Arr::range(3, 7);
        $this->assertEquals(Arr::wrap(array(2 => 3, 3 => 4, 4 => 5)), $a->intersection($b), 'intersection');
        $this->assertEquals(Arr::wrap(array(0 => 3, 1 => 4, 2 => 5)), $b->intersection($a), 'preserves keys of callee');
    }

    public function testDifference()
    {
        $a = Arr::range(1, 5);
        $b = Arr::range(3, 7);
        $this->assertEquals(Arr::wrap(array(1, 2)), $a->difference($b), 'difference');
        $this->assertEquals(Arr::wrap(array(3 => 6, 4 => 7)), $b->difference($a), 'preserves keys of callee');
    }


    // ----- Assertions -----

    public function testAll()
    {
        $p = function ($x) { return $x < 6; };

        $this->assertTrue(Arr::all(range(1, 5), $p));
        $this->assertFalse(Arr::all(range(1, 7), $p));

        $this->assertTrue(Arr::range(1, 5)->all($p));
        $this->assertFalse(Arr::range(1, 7)->all($p));
    }

    public function testAny()
    {
        $p = function ($x) { return $x % 4 === 0; };

        $this->assertTrue(Arr::any(range(1, 5), $p));
        $this->assertFalse(Arr::any(range(1, 3), $p));

        $this->assertTrue(Arr::range(1, 5)->any($p));
        $this->assertFalse(Arr::range(1, 3)->any($p));
    }

    public function testNone()
    {
        $p = function ($x) { return $x % 4 === 0; };

        $this->assertFalse(Arr::none(range(1, 5), $p));
        $this->assertTrue(Arr::none(range(1, 3), $p));

        $this->assertFalse(Arr::range(1, 5)->none($p));
        $this->assertTrue(Arr::range(1, 3)->none($p));
    }

    public function testOne()
    {
        $p = function ($x) { return $x % 3 === 0; };

        $this->assertFalse(Arr::one(range(1, 2), $p));
        $this->assertTrue(Arr::one(range(1, 5), $p));
        $this->assertFalse(Arr::one(range(1, 6), $p));

        $this->assertFalse(Arr::range(1, 2)->one($p));
        $this->assertTrue(Arr::range(1, 5)->one($p));
        $this->assertFalse(Arr::range(1, 6)->one($p));
    }

    public function testExactly()
    {
        $p = function ($x) { return $x % 2 === 0; };

        $this->assertFalse(Arr::exactly(range(1, 3), 2, $p));
        $this->assertTrue(Arr::exactly(range(1, 4), 2, $p));
        $this->assertFalse(Arr::exactly(range(1, 6), 2, $p));

        $this->assertFalse(Arr::range(1, 3)->exactly(2, $p));
        $this->assertTrue(Arr::range(1, 4)->exactly(2, $p));
        $this->assertFalse(Arr::range(1, 6)->exactly(2, $p));
    }


    // ----- Filtering -----

    public function testFilter()
    {
        $this->assertEquals(
            array(1, 2, 3, 4),
            Arr::wrap(array(1, 2, 0, 3, '', 4))->filter()->toArray(),
            'Filters out falsy values if no callback is passed',
            0, 0, true
        );
        $this->assertEquals(
            array(3, 5, 7),
            Arr::range(3, 8)->filter(function ($x) { return $x % 2; })->toArray(),
            'Filter with callback',
            0, 0, true
        );
        $this->assertEquals(
            array(1, 3),
            Arr::range(1, 8)->filterWithKey(function ($v, $k) { return $v % 2 && $k < 4; })->toArray(),
            'Filter with key',
            0, 0, true
        );
    }

    public function testSample()
    {
        $this->assertEquals(3, Arr::range(1, 5)->sample(3)->length(), "Selects multiple elements correctly");
        $this->assertEquals(1, Arr::range(1, 5)->sample(1)->length(), "Selects an array of a single element when size is 1");
        $this->assertEquals(3, Arr::wrap(array(3, 3, 3))->sample(), "Selects a single element when size is omitted");

        $this->assertEquals(3, count(Arr::sample(range(1, 5), 3)), "Selects multiple elements correctly");
        $this->assertEquals(1, count(Arr::sample(range(1, 5), 1)), "Selects an array of a single element when size is 1");
        $this->assertEquals(3, Arr::sample(array(3, 3, 3)), "Selects a single element when size is omitted");
    }


    // ----- Mapping -----

    public function testMap()
    {
        $this->assertEquals(
            array(4, 9, 16),
            Arr::wrap(array(2, 3, 4))->map(function ($x) { return $x * $x; })->toArray()
        );

        $this->assertEquals(
            array('a' => 'a1', 'b' => 'b2', 'c' => 'c3'),
            Arr::wrap(array('a' => 1, 'b' => 2, 'c' => 3))->mapWithKey(function ($v, $k) { return $k.$v; })->toArray()
        );

        $this->assertEquals(
            array('a' => 'a1', 'b' => 'b2', 'c' => 'c3'),
            Arr::mapWithKey(array('a' => 1, 'b' => 2, 'c' => 3), function ($v, $k) { return $k.$v; })
        );
    }

    public function testFlatMap()
    {
        $this->assertEquals(
            array('foo', 'bar', 'baz'),
            Arr::flatMap(array('foo', 'bar baz'), function ($s) { return explode(' ', $s); })
        );
    }

    public function testPluck()
    {
        $friends = $this->getFriends();
        $wrap = Arr::wrap($friends);

        $this->assertEquals(array('Alice', 'Bob', 'Harry'), Arr::pluck($friends, 'name'));
        $this->assertEquals(array('Alice', 'Bob', 'Harry'), $wrap->pluck('name')->toArray());
        $this->assertEquals(array('Alice' => 33, 'Bob' => 29, 'Harry' => 33), Arr::pluck($friends, 'age', 'name'));
        $this->assertEquals(array('Alice' => 33, 'Bob' => 29, 'Harry' => 33), $wrap->pluck('age', 'name')->toArray());
    }

    public function testMapToAssoc()
    {
        $this->assertEquals(
            array('A' => 4, 'B' => 5, 'C' => 6),
            Arr::mapToAssoc(
                array('a' => 1, 'b' => 2, 'c' => 3),
                function ($v, $k) { return array(strtoupper($k), $v + 3); }
            )
        );

        $this->assertEquals(
            array('A' => 4, 'B' => 5, 'C' => 6),
            Arr::wrap(array('a' => 1, 'b' => 2, 'c' => 3))
                ->mapToAssoc(function ($v, $k) { return array(strtoupper($k), $v + 3); })
                ->toArray()
        );
    }

    // ----- Folding and reduction -----

    public function testFold()
    {
        $concat = function ($r, $e) { return $r.$e; };
        $triple = function ($acc, $v, $k) { return $acc.$k.$v; };

        $this->assertEquals('123', Arr::range(1, 3)->fold($concat), 'left fold with default initial');
        $this->assertEquals('0123', Arr::range(1, 3)->fold($concat, '0'), 'left fold with custom initializer');
        $this->assertEquals('0a1b2c', Arr::range('a', 'c')->foldWithKey($triple), 'fold with keys');

        $this->assertEquals('321', Arr::foldRight(range(1, 3), $concat), 'static right fold with default initial');
        $this->assertEquals('0321', Arr::foldRight(range(1, 3), $concat, '0'), 'static right fold with custom initial');
        $this->assertEquals('2c1b0a', Arr::foldRightWithKey(range('a', 'c'), $triple), 'static right fold with keys');

        $this->assertEquals('321', Arr::range(1, 3)->foldRight($concat), 'right fold with default initial');
        $this->assertEquals('0321', Arr::range(1, 3)->foldRight($concat, '0'), 'right fold with custom initial');
        $this->assertEquals('2c1b0a', Arr::range('a', 'c')->foldRightWithKey($triple), 'right fold with keys');
    }

    public function testSum()
    {
        $this->assertEquals(21, Arr::range(1, 6)->sum());
        $this->assertEquals(21, Arr::wrap(array('tasty', 'big', 'cheeseburgers'))->sum(function ($x) { return mb_strlen($x); }));
    }

    public function testMin()
    {
        $this->assertEquals(-1, Arr::wrap(array(3, -1, 4, 2))->min(), 'Finds minimum');
        $this->assertEquals(999, Arr::wrap(range(999, 1005))->min(), 'Finds large minimum');
        $this->assertEquals('a', Arr::wrap(array('a', 'b', 'cde'))->min(), 'Works with strings');
        $food = array('tasty', 'big', 'cheeseburgers');
        $this->assertEquals('big', Arr::wrap($food)->min(function ($x) { return mb_strlen($x); }), 'Works with callback');
        $this->assertEquals('big', Arr::minBy($food, function ($x) { return mb_strlen($x); }), 'Static minBy');
    }

    public function testMaxBy()
    {
        $this->assertEquals(4, Arr::wrap(array(3, -1, 4, 2))->max(), 'Finds maximum');
        $this->assertEquals(-999, Arr::wrap(range(-1005, -999))->max(), 'Finds small maximum');
        $this->assertEquals('cde', Arr::wrap(array('a', 'b', 'cde'))->max(), 'Works with strings');
        $food = array('tasty', 'big', 'cheeseburgers');
        $this->assertEquals('cheeseburgers', Arr::wrap($food)->max(function ($x) { return mb_strlen($x); }), 'Works with callback');
        $this->assertEquals('cheeseburgers', Arr::maxBy($food, function ($x) { return mb_strlen($x); }), 'Static maxBy');
    }

    public function testJoin()
    {
        $range = Arr::range(1, 3);
        $this->assertEquals($range->join(), '123', 'With default separator');
        $this->assertEquals($range->join(', '), '1, 2, 3', 'With custom separator');
    }


    // ----- Splitting -----

    public function testPartition()
    {
        list($odd, $even) = Arr::range(1, 5)->partition(function ($x) { return $x % 2; });
        /** @var $odd WrappedArray */
        /** @var $even WrappedArray */

        $this->assertEquals(array(1, 3, 5), $odd->values()->toArray());
        $this->assertEquals(array(2, 4), $even->values()->toArray());

        $this->assertEquals(
            array(array(1, 3, 5), array(2, 4)),
            Arr::partition(range(1, 5), function ($x) { return $x % 2; }),
            'static version',
            0, 0, true
        );
    }

    public function testChunk()
    {
        $this->assertEquals(
            array(array(1, 2), array(3, 4), array(5, 6)),
            Arr::wrap(range(1, 6))->chunk(2)->toArray(true)
        );
        $this->assertEquals(
            array(array(1, 2), array(3, 4), array(5)),
            Arr::wrap(range(1, 5))->chunk(2)->toArray(true)
        );
    }

    public function testSliding()
    {
        $this->assertEquals(array(array(1, 2, 3), array(2, 3, 4), array(3, 4, 5)), Arr::sliding(range(1, 5), 3)->toArray());
        $this->assertEquals(array(array(1, 2, 3, 4), array(4, 5)), Arr::sliding(range(1, 5), 4, 3)->toArray());
        $this->assertEquals(array(array(1, 2, 3), array(2, 3, 4), array(3, 4, 5)), Arr::range(1, 5)->sliding(3)->toArray(true));
        $this->assertEquals(array(array(1, 2, 3, 4), array(4, 5)), Arr::range(1, 5)->sliding(4, 3)->toArray(true));
    }

    // ----- Zipping -----

    public function testZip()
    {
        $a = Arr::range(1, 3);
        $b = Arr::range(4, 6);
        $c = array(
            array(1, 4),
            array(2, 5),
            array(3, 6),
        );
        $this->assertEquals($c, $a->zip($b)->toArray());
    }

    public function testZipWith()
    {
        $a = Arr::range(1, 3);
        $b = Arr::range(4, 6);
        $c = array(5, 7, 9);
        $this->assertEquals($c, $a->zipWith($b, function($a, $b) { return $a + $b; })->toArray());
    }


    // ----- Sorting -----

    // TODO preserveKeys tests


    public function testFlatten()
    {
        $this->assertEquals(
            array(3, 4, 5, 4, 6, 5),
            Arr::wrap(array(array(3, 4, 5), array(4, 6, 5)))->flatten()->toArray()
        );
    }

    public function testSort()
    {
        $sortable = Arr::wrap(array(
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


    public function testLength()
    {
        $this->assertEquals(3, Arr::wrap(array(1, 2, 3))->length());
    }


}
