<?php
namespace Axelarge\ArrayTools;

use ArrayAccess;
use IteratorAggregate;

interface ArrLike extends ArrayAccess, IteratorAggregate
{
    /**
     * Creates a new instance by wrapping the given array
     *
     * @param array $array
     * @return static
     */
    public static function wrap(array $array);


    /**
     * Returns the underlying array
     *
     * @param bool $recursive
     * @return array
     */
    public function toArray($recursive = false);

    /**
     * Returns a string representation of the contents
     *
     * @return mixed
     */
    public function __toString();

    /**
     * Returns a clone of the object
     *
     * @return static
     */
    public function dup();


    // ----- Length checking methods -----

    /**
     * Returns the length of the array
     *
     * @return int
     */
    public function length();

    /**
     * Checks if the array is empty
     *
     * @return bool
     */
    public function isEmpty();

    // prefixLength
    // segmentLength


    // ----- Traversal -----

    /**
     * Invokes $callback for each element and its key in the array.
     *
     * @param callable $callback Will receive ($value, $key). The return value is ignored
     * @return static
     */
    public function each($callback);

    /**
     * Runs a callback for each element in the array
     *
     * Passes the element as the first argument and an incrementing index as the second
     *
     * <code>
     * Arr::wrap(['a', 'b', 'c'])->eachWithIndex(function ($e, $idx) { echo "$idx $e "; }
     * // outputs "0 a 1 b 2 c "
     * </code>
     *
     * @param callable $callback Will receive ($value, $key). The return value is ignored
     * @return static
     */
    public function eachWithIndex($callback);

    /**
     * Invokes a callback passing $this as the argument, ignoring the return value.
     *
     * Useful for debugging in the middle of a chain.
     * Can also be used to modify the object, although doing so is discouraged.
     *
     * <code>
     * Arr::wrap(range(1, 10))
     *      ->filter(function ($v) { return $v % 2 != 0; })
     *      ->tap(function ($arr) { $arr->unshift(0); }) // Add back zero
     *      ->map(function ($v) { return $v * $v; })
     *      ->tap(function ($arr) { var_dump($arr); }) // Debug without breaking the method chain
     *      ->sum();
     * </code>
     *
     * @param callable $callback
     * @return static $this
     */
    public function tap($callback);


    // ----- Single element access -----

    /**
     * Returns the value at the given index
     *
     * @param string|int $key
     * @return mixed
     */
    public function get($key);

    /**
     * Inserts a value for the given key
     *
     * @param string|int $key
     * @param mixed $value
     * @return static
     */
    public function put($key, $value);

    /**
     * Retrieves a nested element from the array or $default if it doesn't exist
     *
     * <code>
     * $friends = Arr::wrap([
     *      'Alice' => ['age' => 33, 'hobbies' => ['biking', 'skiing']],
     *      'Bob' => ['age' => 29],
     * ]);
     *
     * $friends->getNested('Alice.hobbies.1'); //=> 'skiing'
     * $friends->getNested(['Alice', 'hobbies', 1]); //=> 'skiing'
     * $friends->getNested('Bob.hobbies.0', 'none'); //=> 'none'
     * </code>
     *
     * @param string|array $keys
     * @param mixed $default
     * @return mixed
     */
    public function getNested($keys, $default = null);

    /**
     * Returns the value at the given index or $default if it's not present
     *
     * @param int|string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOrElse($key, $default = null);

    /**
     * Returns the value at the given index. If not present, inserts $default and returns it
     *
     * @param int|string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOrPut($key, $default = null);

    /**
     * Deletes and returns a value from an array
     *
     * @param int|string $key
     * @param mixed $default
     * @return mixed
     */
    public function getAndDelete($key, $default = null);

    /**
     * Returns the first value
     *
     * @return mixed
     */
    public function first();

    /**
     * Returns the last value
     *
     * @return mixed
     */
    public function last();


    // ----- Slicing -----

    /**
     * Returns the first $n elements or the last -$n elements if $n is negative
     *
     * @param int $n
     * @return static
     */
    public function take($n);

    /**
     * Returns longest prefix of elements that satisfy the $predicate.
     *
     * The predicate will be passed value and key of each element.
     *
     * @param callable $predicate ($value, $key => bool)
     * @return static
     */
    public function takeWhile($predicate);

    /**
     * Returns all but the first $n elements or all but the last -$n elements if $n is negative
     *
     * @param int $n
     * @return static
     */
    public function drop($n);

    /**
     * Drops longest prefix of elements satisfying $predicate and returns the rest.
     *
     * @param callable $predicate ($value, $key => bool)
     * @return static
     */
    public function dropWhile($predicate);

    /**
     * Returns a slice of the array
     *
     * @param int $offset
     * @param int $length
     * @param bool $preserveKeys
     * @return static
     */
    public function slice($offset, $length = null, $preserveKeys = false);

    /**
     * Replaces part of the array with another array
     *
     * @param int $offset
     * @param int $length
     * @param array|ArrLike|null $replacement
     * @return static
     */
    public function splice($offset, $length = null, $replacement = null);

    /**
     * Reverses the array
     *
     * @param bool $preserveKeys
     * @return static
     */
    public function reverse($preserveKeys = false);

    /**
     * Repeats the array $n times
     *
     * @param int $n
     * @return static
     */
    public function repeat($n);
    // cycle


    // ----- Finding -----

    /**
     * Checks if the key exists in the array
     *
     * @param int|string $key
     * @return bool
     */
    public function hasKey($key);

    /**
     * Checks if the value exists in the array
     *
     * @param mixed $value
     * @param bool $strict Whether to use strict comparison for determining equality
     * @return bool
     */
    public function hasValue($value, $strict = true);

    // hasSlice/containsSlice
    // indexOfSlice/lastIndexOfSlice

    /**
     * Returns the first value satisfying the predicate or $default
     *
     * @param callable $predicate ($value, $key) -> bool
     * @param mixed $default
     * @return mixed
     */
    public function find($predicate, $default = null);

    /**
     * Returns the last value satisfying the predicate or $default
     *
     * @param callable $predicate ($value, $key) -> bool
     * @param mixed $default
     * @return mixed
     */
    public function findLast($predicate, $default = null);

    /**
     * Returns the key satisfying the predicate or null
     *
     * @param callable $predicate ($value, $key) -> bool
     * @return int|string|null
     */
    public function findKey($predicate);

    /**
     * Returns the last key satisfying the predicate or null
     *
     * @param callable $predicate ($value, $key) -> bool
     * @return int|string|null
     */
    public function findLastKey($predicate);

    /**
     * Returns the key of the value in the array or null if the value is not found
     *
     * @param mixed $value
     * @param bool $strict Whether to use strict comparison
     * @return int|string|null
     */
    public function indexOf($value, $strict = true);

    /**
     * Returns the last position of the value in the array or null if the value is not found
     *
     * @param mixed $value
     * @param bool $strict Whether to use strict comparison
     * @return int|string|null
     */
    public function lastIndexOf($value, $strict = true);


    // ----- Hash operations -----

    /**
     * Returns the keys of the array
     *
     * @return static
     */
    public function keys();

    /**
     * Returns the values of the array
     *
     * @return static
     */
    public function values();

    /**
     * Returns only those values whose keys are present in $keys
     *
     * Scalar values can also be passed as multiple arguments
     * <code>
     * Arr::create(range('a', 'e'))->only(3, 4); //=> ['d', 'e']
     * </code>
     *
     * @param array|ArrLike|mixed $keys
     * @return static
     */
    public function only($keys);

    /**
     * Returns only those values whose keys are not present in $keys
     *
     * Scalar values can also be passed as multiple arguments
     * <code>
     * Arr::create(range('a', 'e'))->except(2, 4); //=> ['a', 'b', 'd']
     * </code>
     *
     * @param array|ArrLike|mixed $keys
     * @return static
     */
    public function except($keys);

    /**
     * Re-indexes the array by either results of a callback or a sub-key.
     *
     * If multiple entries return the same key, the last one is kept.
     *
     * <code>
     * $friends = Arr::wrap([
     *      ['name' => 'Alice', 'age' => 33],
     *      ['name' => 'Bob', 'age' => 29],
     *      ['name' => 'Harry', 'age' => 33],
     * ]);
     *
     * $friends->indexBy('name');
     * //=> [Alice => [name => Alice, age => 33], Bob => [name => Bob, age => 29], Harry => [name => Harry, age => 33]]
     * $friends->indexBy('age');
     * //=> [33 => [name => Harry, age => 33], 29 => [name => Bob, age => 29]]
     * </code>
     *
     * @param callable|string $callbackOrKey ($value, $key) -> number|string
     * @param bool $arrayAccess Whether to use array or object access when given a key name ($callbackOrKey is a string)
     * @return static
     */
    public function indexBy($callbackOrKey, $arrayAccess = true);

    /**
     * Groups the array into sets key by either results of a callback or a sub-key
     *
     * @param callable|string $callbackOrKey ($value, $key) -> number|string
     * @param bool $arrayAccess Whether to use array or object access when given a key name ($callbackOrKey is a string)
     * @return static
     */
    public function groupBy($callbackOrKey, $arrayAccess = true);

    /**
     * Merges the array with $other. When two values have identical string keys, the one from $other is taken.
     *
     * @param array|ArrLike $other
     * @return static
     */
    public function merge($other);

    /**
     * Merges $other with the array. When two values have identical string keys, the one from $other is discarded.
     *
     * @param array|ArrLike $other
     * @return static
     */
    public function reverseMerge($other);

    /**
     * Returns a new array using $this as the keys and $values as the values
     *
     * @param array|ArrLike $values
     * @return static
     */
    public function combine($values);

    /**
     * Flips the array, exchanging keys with values
     *
     * @return static
     */
    public function flip();


    // ----- Mutation methods -----
    
    /**
     * Appends an element to the end of the array
     *
     * @param mixed $value
     * @return static
     */
    public function push($value);

    /**
     * Removes the last element of the array and returns it
     *
     * @return mixed
     */
    public function pop();

    /**
     * Prepends an element to the front of the array
     *
     * @param mixed $value
     * @return static
     */
    public function unshift($value);

    /**
     * Removes the first element of the array and returns it
     *
     * @return mixed
     */
    public function shift();


    // ----- Set operations -----
    
    /**
     * Returns those values that are present in both arrays
     *
     * <code>
     * Arr::create(1, 2, 3)->intersection([2, 3, 4]) //=> [2, 3]
     * </code>
     *
     * @param array|ArrLike $other
     * @return static
     */
    public function intersection($other);

    /**
     * Returns those values that are not present in the other array
     *
     * <code>
     * Arr::create(1, 2, 3)->difference([2, 3, 4]) //=> [1]
     * </code>
     *
     * @param array|ArrLike $other
     * @return static
     */
    public function difference($other);

//    public function union($other);


    // ----- Combinatorial methods -----
    // combinations
    // permutations
    // variations

    // ----- Assertions -----
    
    /**
     * Returns true if all elements satisfy the given predicate
     *
     * @param callable $predicate
     * @return bool
     */
    public function all($predicate);

    /**
     * Returns true if at least one element satisfies the given predicate
     *
     * @param callable $predicate
     * @return bool
     */
    public function any($predicate);

    /**
     * Returns true if exactly one element satisfies the given predicate
     *
     * @param callable $predicate
     * @return bool
     */
    public function one($predicate);

    /**
     * Returns true if none of the elements satisfy $predicate
     *
     * @param callable $predicate
     * @return bool
     */
    public function none($predicate);

    /**
     * Returns true if exactly $n elements satisfy the given $predicate.
     *
     * @param int $n
     * @param callable $predicate ($value, $key) -> bool
     * @return bool
     */
    public function exactly($n, $predicate);



    // ----- Filtering -----

    /**
     * Filters the array by a predicate
     *
     * @param callable|null $predicate ($value) -> bool If null, checks if the value is not empty
     * @return static
     */
    public function filter($predicate = null);

    /**
     * Filters the array by a predicate
     * Like filter() but also passes in the key of each element
     *
     * @param callable $predicate ($value, $key) -> bool
     * @return static
     */
    public function filterWithKey($predicate);

    /**
     * Returns unique values of the array
     *
     * @return static
     */
    public function unique();

    /**
     * Returns $size random elements from the array or a single element if $size is null.
     *
     * @param int|null $size
     * @return static
     */
    public function sample($size = null);


    // ----- Mapping -----

    /**
     * Map the array into another, applying $callback to each element and its key
     *
     * <code>
     * Arr::wrap([2, 3, 4])->map(function ($x) { return $x * $x; }); //=> [3, 9, 16]
     * </code>
     *
     * @param callable $callback ($value) -> $newValue
     * @return static
     */
    public function map($callback);

    /**
     * Map the array into another, applying $callback to each element and it's key.
     *
     * <code>
     * $friends = ['Bob' => ['age' => 34, 'surname' => 'Hope'], 'Alice' => ['age' => 23, 'surname' => 'Miller']];
     * Arr::wrap($friends)->mapWithKey(function ($v, $k) { return "$v is {$k['age']} years old"; });
     * //=> ['Bob is 34 years old', 'Alice is 23 years old']
     * </code>
     *
     * @param callable $callback ($value, $key) -> $newValue
     * @return static
     */
    public function mapWithKey($callback);

    /**
     * Maps an array into another by applying $callback to each element and flattening the results
     *
     * <code>
     * Arr::wrap(['foo', 'bar baz'])->flatMap(function ($s) { return explode(' ', $s); });
     * //=> ['foo', 'bar', 'baz']
     * </code>
     *
     * @param callable $callback ($value, $key) -> array
     * @return array static
     */
    public function flatMap($callback);

    /**
     * Shortcut method to pick out specified keys/properties from an array of arrays/objects
     *
     * <code>
     * $people = Arr::wrap([
     *      ['name' => 'Bob', 'age' => 23],
     *      ['name' => 'Alice', 'age' => 32],
     *      ['name' => 'Frank', 'age' => 40],
     * ]);
     *
     * $people->pluck('name'); //=> ['Bob', 'Alice', 'Frank']
     * $people->pluck('age', 'name'); //=> ['Bob' => 23, 'Alice' => 32, 'Frank' => 40]
     * </code>
     *
     * @param string $valueAttribute
     * @param string|null $keyAttribute
     * @param bool $arrayAccess Determines whether to use array access ($elem[$prop]) or property access ($elem->$prop)
     * @return static
     */
    public function pluck($valueAttribute, $keyAttribute = null, $arrayAccess = true);

    /**
     * Creates an associative array by invoking $callback on each element and using the 2 resulting values as key and value
     *
     * <code>
     * $friends = [['name' => 'Bob', 'surname' => 'Hope', 'age' => 34], ['name' => 'Alice', 'surname' => 'Miller', 'age' => 23]];
     * Arr::wrap($friends)->mapToAssoc(function ($v, $k) { return [$v['name'].' '.$v['surname'], $v['age']] });
     * //=> ['Bob Hope' => 34, 'Alice Miller' => 23]
     * </code>
     *
     * @param callable $callback ($value, $key) -> array($newKey, $newValue)
     * @return static
     */
    public function mapToAssoc($callback);


    // ----- Folding and reduction -----

    /**
     * Reduces the array into a single value by calling $callback on each element and the previous result.
     *
     * <code>
     * Arr::wrap(range(1, 5))->fold(function ($sum, $element) { return $sum + $element; }, 0); //=> 15
     * Arr::wrap(range(1, 5))->fold(function ($product, $element) { return $product * $element; }, 1); //=> 120
     * Arr::wrap(['foo', 'bar', 'baz'])->fold(function ($res, $e) { return $res . $e; }); //=> 'foobarbaz'
     * </code>
     *
     * @param callable $callback ($accumulator, $value) -> mixed
     * @param mixed $initial
     * @return mixed
     */
    public function fold($callback, $initial = null);

    /**
     * Reduces the array into a single value by calling $callback on each element and the previous result.
     * Like fold(), but also passes the key as the third argument.
     *
     * @param callable $callback ($accumulator, $value, $key) -> mixed
     * @param mixed $initial
     * @return mixed
     */
    public function foldWithKey($callback, $initial = null);

    /**
     * Right-associative version of fold()
     *
     * <code>
     * Arr::wrap(['foo', 'bar', 'baz'])->foldRight(function ($res, $e) { return $res . $e; }); //=> 'bazbarfoo'
     * </code>
     *
     * @param callable $callback ($accumulator, $value) -> mixed
     * @param mixed $initial
     * @return mixed
     */
    public function foldRight($callback, $initial = null);

    /**
     * Right-associative version of foldWithKey()
     *
     * @param callable $callback ($accumulator, $value, $key) -> mixed
     * @param mixed $initial
     * @return mixed
     */
    public function foldRightWithKey($callback, $initial = null);

    // scan/scanRight

    /**
     * Flattens the array, combining elements of all sub-arrays into one array
     *
     * <code>
     * Arr::wrap([[1, 2, 3], [4, 5]])->flatten(); //=> [1, 2, 3, 4, 5]
     * </code>
     *
     * @return static
     */
    public function flatten();

    /**
     * Finds the smallest element
     *
     * <code>
     * Arr::wrap([3, 1, 4, 2])->min(); //=> 1
     * Arr::wrap(['tasty', 'big', 'cheeseburgers'])->min('mb_strlen'); //=> 'big'
     * </code>
     *
     * @param callable|null $callback ($value, $key) -> number|string
     * @return mixed
     */
    public function min($callback = null);

    /**
     * Finds the largest element
     *
     * <code>
     * Arr::wrap([3, 1, 4, 2])->max(); //=> 4
     * Arr::wrap(['tasty', 'big', 'cheeseburgers'])->max('mb_strlen'); //=> 'cheeseburgers'
     * </code>
     *
     * @param callable|null $callback ($value, $key) -> number|string
     * @return mixed
     */
    public function max($callback = null);

    /**
     * Returns the sum of all elements
     *
     * If a callback is given, sums the results of it over each element
     *
     * <code>
     * Arr::wrap(range(1, 6))->sum(); //=> 21
     * Arr::wrap(['tasty', 'big', 'cheeseburgers'])->sum('mb_strlen'); // => 21
     * </code>
     *
     * @param null|callable $callback ($value, $key) -> number
     * @return number
     */
    public function sum($callback = null);

    /**
     * Joins the array values into a string, separated by $separator
     *
     * <code>
     * Arr::wrap(['tasty', 'big', 'cheeseburgers'])->join(' '); //=> 'tasty big cheeseburgers'
     * </code>
     *
     * @param string $separator
     * @return string
     */
    public function join($separator = '');


    // ----- Splitting -----

    /**
     * Returns two arrays: one with elements that satisfy the predicate, the other with elements that don't
     *
     * Use in combination with built-in list() to easily access each array.
     *
     * <code>
     * list ($divisibleBy3, $rest) = Arr::wrap(range(1, 9))->partition(function ($v) { return $v % 3 == 0; });
     * //=> [3, 6, 9], [1, 2, 4, 5, 7, 8]
     * </code>
     *
     * @param callable $predicate ($value, $key) -> bool
     * @return static[]
     */
    public function partition($predicate);

    /**
     * Splits the array into chunks of $size
     *
     * @param int $size
     * @param bool $preserveKeys
     * @return static
     */
    public function chunk($size = 1, $preserveKeys = false);

    /**
     * Creates a sliding window of size $size, advancing $step elements on each iteration
     *
     * <code>
     * $oneToFive = Arr::wrap(range(1, 5));
     * $oneToFive->sliding(3); // [[1, 2, 3], [2, 3, 4], [3, 4, 5]]
     * $oneToFive->sliding(4, 3); // [[1, 2, 3, 4], [4, 5]]
     * </code>
     *
     * @param int $size
     * @param int $step
     * @return ArrLike
     */
    public function sliding($size, $step = 1);


    // splitAt
    // splitWhere
    // span


    // ----- Zipping -----

    /**
     * Zips the array with another
     *
     * <code>
     * Arr::wrap(range(1, 5))->zip(range('a', 'e'));
     * //=> [[1, 'a'], [2, 'b'], [3, 'c'], [4, 'd'], [5, 'e']]
     * </code>
     *
     * @param array|ArrLike $array
     * @return static
     */
    public function zip($array);

    /**
     * Zips the array with another using a function on each pair of elements
     *
     * <code>
     * Arr::wrap(range('a', 'e'))->zipWith(range(1, 5), 'str_repeat');
     * // => [a, bb, ccc, dddd, eeeee]
     * </code>
     *
     * @param array|ArrLike $array
     * @param callable $callback ($elemFromThis, $elemFromArray) -> mixed
     * @return static
     */
    public function zipWith($array, $callback);


    // ----- Sorting -----

    /**
     * Sorts the array in-place
     *
     * @param bool $preserveKeys
     * @param int $mode Sort flags
     * @return static
     */
    public function sort($preserveKeys = false, $mode = SORT_REGULAR);

    /**
     * Returns a sorted copy of the array
     *
     * @param bool $preserveKeys
     * @param int $mode Sort flags
     * @return static
     */
    public function sorted($preserveKeys = false, $mode = SORT_REGULAR);

    /**
     * Sorts the array in-place by a key or result of a callback
     *
     * @param callable|string $callbackOrKey
     * @param int $mode Sort flags
     * @return static
     */
    public function sortBy($callbackOrKey, $mode = SORT_REGULAR);

    /**
     * Returns a copy of the array sorted by a key or result of a callback
     *
     * @param callable|string $callbackOrKey
     * @param int $mode Sort flags
     * @return static
     */
    public function sortedBy($callbackOrKey, $mode = SORT_REGULAR);

    /**
     * Shuffles the array in-place
     *
     * @return static
     */
    public function shuffle();

    /**
     * Returns a shuffled copy of the array
     *
     * @return static
     */
    public function shuffled();


    // ----- Methods for sorted arrays -----
    // binarySearch
    // insert


    // lazy

}
