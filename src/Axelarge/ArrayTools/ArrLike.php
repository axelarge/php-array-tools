<?php
namespace Axelarge\ArrayTools;

interface ArrLike extends \ArrayAccess, \IteratorAggregate
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
     * @return array
     */
    public function toArray();

    /**
     * Returns a clone of the object
     *
     * @return static
     */
    public function dup();

    /**
     * Reverses the array
     *
     * @param bool $preserveKeys
     * @return static
     */
    public function reverse($preserveKeys = false);

    /**
     * Inserts a value for the given key
     *
     * @param string|int $key
     * @param mixed $value
     * @return static
     */
    public function put($key, $value);

    /**
     * Returns the value at the given index
     *
     * @param string|int $key
     * @return mixed
     */
    public function get($key);

    /**
     * Retrieves a nested element from the array or $default if it doesn't exist
     *
     * @param string|array $keys
     * @param mixed $default
     * @return mixed
     */
    public function getNested($keys, $default = null);

    /**
     * Returns the value at the given index or $default if it not present
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

    /**
     * Returns the first value satisfying the predicate or null
     *
     * @param callable $predicate
     * @return mixed|null
     */
    public function find($predicate);

    /**
     * Returns the key satisfying the predicate or null
     *
     * @param callable $predicate
     * @return int|null|string
     */
    public function findKey($predicate);

    /**
     * Returns the position of the value in the array or false if the value is not found
     *
     * @param mixed $value
     * @param bool $strict Whether to use strict comparison
     * @return bool|int|string
     */
    public function indexOf($value, $strict = true);

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
     * @param array|Arr $replacement
     * @return static
     */
    public function splice($offset, $length = null, $replacement = null);

    /**
     * Returns the first $n elements or the last -$n elements if $n is negative
     *
     * @param int $n
     * @return static
     */
    public function take($n);

    /**
     * Returns all but the first $n elements or all but the last -$n elements if $n is negative
     *
     * @param int $n
     * @return static
     */
    public function drop($n);

    /**
     * Returns unique values of the array
     *
     * @return static
     */
    public function unique();

    /**
     * Joins the array values into a string, separated by $separator
     *
     * @param string $separator
     * @return string
     */
    public function join($separator = '');

    /**
     * Repeats the array $n times
     *
     * @param int $n
     * @return static
     */
    public function repeat($n);

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

    /**
     * Returns only those values whose keys are present in $keys
     *
     * Scalar values can also be passed as multiple arguments
     * <code>
     * Arr::create(range('a', 'e'))->only(3, 4); //=> ['d', 'e']
     * </code>
     *
     * @param array|Arr|mixed $keys
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
     * @param array|Arr|mixed $keys
     * @return static
     */
    public function except($keys);

    /**
     * Returns those values that are present in both arrays
     *
     * <code>
     * Arr::create(1, 2, 3)->intersection([2, 3, 4]) //=> [2, 3]
     * </code>
     *
     * @param array|Arr $other
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
     * @param array|Arr $other
     * @return static
     */
    public function difference($other);

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
     * Re-indexes the array by either results of a callback or a sub-key
     *
     * @param callable|string $callbackOrKey
     * @param bool $arrayAccess Whether to use array or object access when given a key name
     * @return static $this
     */
    public function indexBy($callbackOrKey, $arrayAccess = true);

    /**
     * Groups the array into sets key by either results of a callback or a sub-key
     *
     * @param callable|string $callbackOrKey
     * @param bool $arrayAccess Whether to use array or object access when given a key name
     * @return static
     */
    public function groupBy($callbackOrKey, $arrayAccess = true);

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


    // split_by

    /**
     * Returns $size random elements from the array or a single element if $size is null
     * Note that it differs from array_rand() in that it returns an array with a single element if $size is 1
     *
     * @param int|null $size
     * @return static
     */
    public function sample($size = null);

    /**
     * Merges the array with $other. When two values have identical string keys, the one from $other is taken.
     *
     * @param array|Arr $other
     * @return static
     */
    public function merge($other);

    /**
     * Merges $other with the array. When two values have identical string keys, the one from $other is discarded.
     *
     * @param array|Arr $other
     * @return static
     */
    public function reverseMerge($other);

    /**
     * Returns a new array using $this as the keys and $values as the values
     * @param array|Arr $values
     * @return static
     */
    public function combine($values);

    /**
     * Zips the array with another
     *
     * @param array|Arr $array
     * @return static
     */
    public function zip($array);

    /**
     * Flips the array
     *
     * @return static
     */
    public function flip();

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

    /**
     * Splits the array into chunks of $size
     *
     * @param int $size
     * @param bool $preserveKeys
     * @return static
     */
    public function chunk($size = 1, $preserveKeys = false);

    /**
     * Runs a callback for each element in the array
     *
     * Passes the element as the first argument and a incrementing index as the second
     *
     * @param callable $callback
     * @return static
     */
    public function eachWithIndex($callback);

    /**
     * Runs a callback for each key-value pair in the array
     *
     * @param callable $callback
     * @return static
     */
    public function eachPair($callback);

    /**
     * Filters the array by a predicate
     *
     * @param callable $predicate If null, checks if the value is not empty
     * @return static
     */
    public function filter($predicate = null);

    /**
     * Run a callback passing $this as the argument, then return $this. Useful for chaining.
     *
     * @param callable $callback
     * @return static
     */
    public function tap($callback);

    /**
     * Run a callback passing the underlying array as the argument, then return $this. Useful for chaining.
     *
     * @param callable $callback
     * @return static
     */
    public function tapRaw($callback);

    /**
     * Map the array into another, applying $callback to each element
     *
     * @param callable $callback
     * @return static
     */
    public function map($callback);

    /**
     * Map the array into another, applying $callback to each element and it's key.
     * If $createKeys is set to true, the callback should return an array with the key and value for the new element
     *
     * <code>
     * Arr::wrap(['a' => 1, 'b' => 2, 'c' => 3])->mapWithKey(function ($v, $k) { return [strtoupper($k), $v + 3]; }, true);
     * //=> ['A' => 4, 'B' => 5, 'C' => 6]
     * </code>
     *
     * @param callable $callback
     * @param bool $createKeys
     * @return static
     */
    public function mapWithKey($callback, $createKeys = false);

    /**
     * Maps an array into another by applying $callback to each element and flattening the results
     *
     * <code>
     * Arr::wrap(['foo', 'bar baz'])->flatMap(function ($s) { return explode(' ', $s); });
     * //=> ['foo', 'bar', 'baz']
     * </code>
     *
     * @param callable $callback Should return an array
     * @return array array
     */
    public function flatMap($callback);

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

    public function pluck($valueAttribute, $keyAttribute = null, $arrayAccess = true);

    public function fold($callback, $initial = null);

    public function foldRight($callback, $initial = null);


    /**
     * Returns two arrays: one with elements that satisfy the predicate, the other with elements that don't
     *
     * @param callable $predicate
     * @return static[]
     */
    public function partition($predicate);

    /**
     * Finds the smallest element
     *
     * @return mixed
     */
    public function min();

    /**
     * Finds the largest element
     *
     * @return mixed
     */
    public function max();

    /**
     * Finds the element for which the result of the callback is the smallest
     *
     * @param callable $callback
     * @return mixed
     */
    public function minBy($callback);

    /**
     * Finds the element for which the result of the callback is the largest
     *
     * @param callable $callback
     * @return mixed
     */
    public function maxBy($callback);

    /**
     * Returns the sum of all elements
     *
     * @param callable $callback If given, sums the results of this callback over each element
     * @return number
     */
    public function sum($callback = null);

    /**
     * @param array|Arr $array
     * @param callable $callback
     * @return static
     */
    public function zipWith($array, $callback);

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

}
