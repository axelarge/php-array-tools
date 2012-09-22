<?php
namespace Axelarge\ArrayTools;

/**
 * @license MIT License
 * @license www.opensource.org/licenses/MIT
 */
class Arr
{
    private function __construct() {}

    /**
     * Creates a new WrappedArray instance from the given array
     *
     * @param array $array
     * @return WrappedArray
     */
    public static function wrap(array $array)
    {
        return new WrappedArray($array);
    }

    /**
     * Short-hand for wrap()
     *
     * @deprecated Use wrap() instead
     * @see wrap()
     * @param array $array
     * @return WrappedArray
     */
    public static function w(array $array)
    {
        return new WrappedArray($array);
    }

    /**
     * Create a new instance from function arguments
     *
     * @deprecated Use wrap() instead
     * @return WrappedArray
     */
    public static function create()
    {
        return new WrappedArray(func_get_args());
    }

    public static function range($from, $to, $step = null)
    {
        return new WrappedArray($step === null ? range($from, $to) : range($from, $to, $step));
    }

    /**
     * Returns an easily readable string representation of a nested array structure.
     *
     * @param array $array
     * @param bool|null $showKeys Whether to output array keys. Skip to handle intelligently
     * @return string
     */
    public static function toString($array, $showKeys = null)
    {
        $idx = 0;
        return sprintf('[%s]', implode(', ', static::mapWithKey($array, function ($v, $k) use (&$idx, $showKeys) {
            $str = ($showKeys === null && $idx++ === $k || $showKeys === false) ? '' : "$k => ";

            if (is_array($v)) $str .= Arr::toString($v, $showKeys);
            else if (is_object($v)) $str .= is_callable($v, '__toString') ? (string)$v : get_class($v);
            else $str .= (string)$v;

            return $str;
        })));
    }


    // ----- Traversal -----

    /**
     * Runs a callback for each element in the array
     *
     * Passes the element as the first argument and an incrementing index as the second
     *
     * <code>
     * Arr::eachWithIndex(['a', 'b', 'c'], function ($e, $idx) { echo "$idx $e "; }
     * // outputs "0 a 1 b 2 c "
     * </code>
     *
     * @param array $array
     * @param callable $callback
     * @return static
     */
    public static function eachWithIndex($array, $callback)
    {
        $i = 0;
        foreach ($array as $item) {
            $callback($item, $i++);
        }
    }


    // ----- Single element access -----

    /**
     * Retrieves a nested element from an array or $default if it doesn't exist
     *
     * <code>
     * $friends = [
     *      'Alice' => ['age' => 33, 'hobbies' => ['biking', 'skiing']],
     *      'Bob' => ['age' => 29],
     * ];
     *
     * Arr::getNested($friends, 'Alice.hobbies.1'); //=> 'skiing'
     * Arr::getNested($friends, ['Alice', 'hobbies', 1]); //=> 'skiing'
     * Arr::getNested($friends, 'Bob.hobbies.0', 'none'); //=> 'none'
     * </code>
     *
     * @param array $array
     * @param string|array $keys The key path as either an array or a dot-separated string
     * @param mixed $default
     * @return mixed
     */
    public static function getNested($array, $keys, $default = null)
    {
        if (is_string($keys)) {
            $keys = explode('.', $keys);
        } else if ($keys === null) {
            return $array;
        }

        foreach ($keys as $key) {
            if (is_array($array) && array_key_exists($key, $array)) {
                $array = $array[$key];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * Returns the value at the given index or $default if it not present
     *
     * @param array $array
     * @param int|string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getOrElse($array, $key, $default = null)
    {
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }

    /**
     * Returns the value at the given index. If not present, inserts $default and returns it
     *
     * @param array $array
     * @param int|string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getOrPut(&$array, $key, $default = null)
    {
        if (!array_key_exists($key, $array)) {
            $array[$key] = $default;
        }

        return $array[$key];
    }

    /**
     * Deletes and returns a value from an array
     *
     * @param array $array
     * @param int|string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getAndDelete(&$array, $key, $default = null)
    {
        if (array_key_exists($key, $array)) {
            $result = $array[$key];
            unset($array[$key]);
            return $result;
        } else {
            return $default;
        }
    }


    // ----- Slicing -----

    /**
     * Returns longest prefix of elements that satisfy the $predicate.
     *
     * The predicate will be passed value and key of each element.
     *
     * @param array $array
     * @param callable $predicate ($value, $key) -> bool
     * @return array
     */
    public static function takeWhile($array, $predicate)
    {
        $n = 0;
        foreach ($array as $key => $value) {
            if (!$predicate($value, $key)) break;
            ++$n;
        }

        return array_slice($array, 0, $n);
    }

    /**
     * Drops longest prefix of elements satisfying $predicate and returns the rest.
     *
     * @param array $array
     * @param callable $predicate ($value, $key) -> bool
     * @return array
     */
    public static function dropWhile($array, $predicate)
    {
        $n = 0;
        foreach ($array as $key => $val) {
            if (!$predicate($val, $key)) break;
            ++$n;
        }

        return array_slice($array, $n);
    }

    /**
     * Repeats the array $n times.
     *
     * TODO: Convert to iterator to conserve memory and time
     *
     * @param array $array
     * @param int $n
     * @return array
     */
    public static function repeat($array, $n)
    {
        $result = array();
        while ($n-- > 0) {
            foreach ($array as $value) {
                $result[] = $value;
            }
        }

        return $result;
    }


    // ----- Finding -----

    /**
     * Returns the first value of the array satisfying the $predicate or $default
     *
     * @param array $array
     * @param callable $predicate ($value, $key) -> bool
     * @param mixed $default
     * @return mixed|null
     */
    public static function find($array, $predicate, $default = null)
    {
        foreach ($array as $key => $value) {
            if ($predicate($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Returns the last value of the array satisfying the $predicate or $default
     *
     * @param array $array
     * @param callable $predicate ($value, $key) -> bool
     * @param mixed $default
     * @return mixed|null
     */
    public static function findLast($array, $predicate, $default = null)
    {
        return self::find(array_reverse($array, true), $predicate, $default);
    }

    /**
     * Returns the first key satisfying the $predicate or null
     *
     * @param array $array
     * @param callable $predicate ($value, $key) -> bool
     * @return int|null|string
     */
    public static function findKey($array, $predicate)
    {
        foreach ($array as $key => $value) {
            if ($predicate($value, $key)) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Returns the last key satisfying the $predicate or null
     *
     * @param array $array
     * @param callable $predicate ($value, $key) -> bool
     * @return int|null|string
     */
    public static function findLastKey($array, $predicate)
    {
        return self::findKey(array_reverse($array, true), $predicate);
    }

    public static function lastIndexOf($array, $value, $strict = true)
    {
        $index = array_search($value, array_reverse($array, true), $strict);
        return $index === false ? null : $index;
    }


    // ----- Hash operations -----

    /**
     * Returns only those values whose keys are present in $keys
     *
     * <code>
     * Arr::only(range('a', 'e'), [3, 4]); //=> ['d', 'e']
     * </code>
     *
     * @param array $array
     * @param array $keys
     * @return array
     */
    public static function only($array, $keys)
    {
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * Returns only those values whose keys are not present in $keys
     *
     * <code>
     * Arr::except(range('a', 'e'), [2, 4]); //=> ['a', 'b', 'd']
     * </code>
     *
     * @param array $array
     * @param array $keys
     * @return array
     */
    public static function except($array, $keys)
    {
        return array_diff_key($array, array_flip($keys));
    }

    /**
     * Re-indexes the array by either results of the callback or a sub-key
     *
     * @param array $array
     * @param callable|string $callbackOrKey
     * @param bool $arrayAccess Whether to use array or object access when given a key name
     * @return array
     */
    public static function indexBy($array, $callbackOrKey, $arrayAccess = true)
    {
        $indexed = array();

        if (is_string($callbackOrKey)) {
            if ($arrayAccess) {
                foreach ($array as $element) {
                    $indexed[$element[$callbackOrKey]] = $element;
                }
            } else {
                foreach ($array as $element) {
                    $indexed[$element->{$callbackOrKey}] = $element;
                }
            }
        } else {
            foreach ($array as $element) {
                $indexed[$callbackOrKey($element)] = $element;
            }
        }

        return $indexed;
    }

    /**
     * Groups the array into sets key by either results of a callback or a sub-key
     *
     * @param array $array
     * @param callable|string $callbackOrKey
     * @param bool $arrayAccess Whether to use array or object access when given a key name
     * @return array
     */
    public static function groupBy($array, $callbackOrKey, $arrayAccess = true)
    {
        $groups = array();

        if (is_string($callbackOrKey)) {
            if ($arrayAccess) {
                foreach ($array as $element) {
                    $groups[$element[$callbackOrKey]][] = $element;
                }
            } else {
                foreach ($array as $element) {
                    $groups[$element->{$callbackOrKey}][] = $element;
                }
            }
        } else {
            foreach ($array as $element) {
                $groups[$callbackOrKey($element)][] = $element;
            }
        }

        return $groups;
    }


    // ----- Assertions -----

    /**
     * Returns true if all elements satisfy the given predicate
     *
     * @param $array
     * @param callable $predicate
     * @return bool
     */
    public static function all($array, $predicate)
    {
        foreach ($array as $key => $value) {
            if (!$predicate($value, $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if at least one element satisfies the given predicate
     *
     * @param $array
     * @param callable $predicate
     * @return bool
     */
    public static function any($array, $predicate)
    {
        foreach ($array as $key => $value) {
            if ($predicate($value, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if exactly one element satisfies the given predicate
     *
     * @param $array
     * @param callable $predicate
     * @return bool
     */
    public static function one($array, $predicate)
    {
        return self::exactly($array, 1, $predicate);
    }

    /**
     * Returns true if none of the elements satisfy $predicate
     *
     * @param array $array
     * @param callable $predicate
     * @return bool
     */
    public static function none($array, $predicate)
    {
        return self::exactly($array, 0, $predicate);
    }

    /**
     * Returns true if exactly $n elements satisfy the $predicate
     *
     * @param array $array
     * @param int $n
     * @param callable $predicate ($value, $key) -> bool
     * @return bool
     */
    public static function exactly($array, $n, $predicate)
    {
        $found = 0;
        foreach ($array as $key => $value) {
            if ($predicate($value, $key)) {
                if (++$found > $n) return false;
            }
        }

        return $found == $n;
    }


    // ----- Filtering -----

    /**
     * Keeps only those elements that satisfy the $predicate
     *
     * Differs from array_filter() in that the key of each element is also passed to the predicate.
     *
     * @param array $array
     * @param callable $predicate ($value, $key) -> bool
     * @return array
     */
    public static function filterWithKey($array, $predicate)
    {
        $result = array();
        foreach ($array as $key => $value) {
            if ($predicate($value, $key)) $result[$key] = $value;
        }

        return $result;
    }

    /**
     * Returns $size random elements from the array or a single element if $size is null
     *
     * This function differs from array_rand() in that it returns an array with a single element if $size is 1.
     *
     * @param array $array
     * @param int|null $size
     * @return array
     */
    public static function sample($array, $size = null)
    {
        return $size === null
            ? $array[array_rand($array)]
            : static::only($array, (array)array_rand($array, $size));
    }


    // ----- Mapping -----

    /**
     * Map the collection into another, applying $callback to each element and its key.
     *
     * This function differs from the built-in array_map() in that it also passes the key as a
     * second element to the callback.
     *
     * <code>
     * Arr::map(['a' => 1, 'b' => 2, 'c' => 3], function ($v) { return $v * 2; });
     * //=> ['a' => 2, 'b' => 4, 'c' => 6]
     * </code>
     *
     * @param array $array
     * @param callable $callback
     * @return array
     */
    public static function mapWithKey($array, $callback)
    {
        $mapped = array();
        foreach ($array as $key => $value) {
            $mapped[$key] = $callback($value, $key);
        }

        return $mapped;
    }

    /**
     * Maps an array into another by applying $callback to each element and flattening the results
     *
     * <code>
     * Arr::flatMap(['foo', 'bar baz'], function ($s) { return explode(' ', $s); });
     * //=> ['foo', 'bar', 'baz']
     * </code>
     *
     * @param array $array
     * @param callable $callback ($value, $key) -> array
     * @return array array
     */
    public static function flatMap($array, $callback)
    {
        $result = array();
        foreach ($array as $key => $value) {
            $newValues = $callback($value, $key);
            if ($newValues) {
                foreach ($newValues as $newValue) {
                    $result[] = $newValue;
                }
            }
        }

        return $result;
    }

    /**
     * Shortcut method to pick out specified keys/properties from an array of arrays/objects
     *
     * <code>
     * $people = [
     *      ['name' => 'Bob', 'age' => 23],
     *      ['name' => 'Alice', 'age' => 32],
     *      ['name' => 'Frank', 'age' => 40],
     * ];
     *
     * Arr::pluck($people, 'name'); //=> ['Bob', 'Alice', 'Frank']
     * Arr::pluck($people, 'age', 'name'); //=> ['Bob' => 23, 'Alice' => 32, 'Frank' => 40]
     * </code>
     *
     * @param array $array
     * @param string $valueAttribute
     * @param string|null $keyAttribute
     * @param bool $arrayAccess Determines whether to use array access ($elem[$prop]) or property access ($elem->$prop)
     * @return array
     */
    public static function pluck($array, $valueAttribute, $keyAttribute = null, $arrayAccess = true)
    {
        $result = array();
        if ($arrayAccess) {
            if ($keyAttribute) {
                foreach ($array as $value) {
                    $result[$value[$keyAttribute]] = $value[$valueAttribute];
                }
            } else {
                foreach ($array as $key => $value) {
                    $result[$key] = $value[$valueAttribute];
                }
            }
        } else {
            if ($keyAttribute) {
                foreach ($array as $value) {
                    $result[$value->{$keyAttribute}] = $value->{$valueAttribute};
                }
            } else {
                foreach ($array as $key => $value) {
                    $result[$key] = $value->{$valueAttribute};
                }
            }
        }

        return $result;
    }

    /**
     * Creates an associative array by invoking $callback on each element and using the 2 resulting values as key and value
     *
     * <code>
     * $friends = [['name' => 'Bob', 'surname' => 'Hope', 'age' => 34], ['name' => 'Alice', 'surname' => 'Miller', 'age' => 23]];
     * Arr::mapToAssoc($friends, function ($v, $k) { return [$v['name'].' '.$v['surname'], $v['age']] });
     * //=> ['Bob Hope' => 34, 'Alice Miller' => 23]
     * </code>
     *
     * @param array $array
     * @param callable $callback ($value, $key) -> array($newKey, $newValue)
     * @return array
     */
    public static function mapToAssoc($array, $callback)
    {
        $mapped = array();
        foreach ($array as $key => $value) {
            list($newKey, $newValue) = $callback($value, $key);
            $mapped[$newKey] = $newValue;
        }

        return $mapped;
    }

    /**
     * Flattens the array, combining elements of all sub-arrays into one array
     *
     * <code>
     * Arr::flatten([[1, 2, 3], [4, 5]]); //=> [1, 2, 3, 4, 5]
     * </code>
     *
     * @param array $array
     * @return array
     */
    public static function flatten($array)
    {
        return call_user_func_array('array_merge', $array);
    }


    // ----- Folding and reduction -----

    /**
     * Reduces the array into a single value by calling $callback repeatedly on the elements and their keys, passing the resulting value along each time.
     *
     * <code>
     * Arr::foldRight(['foo', 'bar', 'baz'], function ($res, $v, $k) { return "$res $k:$e"; }); //=> ' 0:foo 1:bar 2:baz'
     * </code>
     *
     * @param array $array
     * @param callable $callback ($accumulator, $value, $key) -> mixed
     * @param mixed $initial
     * @return mixed
     */
    public static function foldWithKey($array, $callback, $initial = null)
    {
        foreach ($array as $key => $value) {
            $initial = $callback($initial, $value, $key);
        }

        return $initial;
    }

    /**
     * Right-associative version of array_reduce().
     *
     * <code>
     * Arr::foldRight(['foo', 'bar', 'baz'], function ($res, $e) { return $res . $e; }); //=> 'bazbarfoo'
     * </code>
     *
     * @param array $array
     * @param callable $callback ($accumulator, $value, $key) -> mixed
     * @param mixed $initial
     * @return mixed
     */
    public static function foldRight($array, $callback, $initial = null)
    {
        return array_reduce(array_reverse($array, true), $callback, $initial);
    }

    /**
     * Right-associative version of foldWithKey()
     *
     * <code>
     * Arr::foldRight(['foo', 'bar', 'baz'], function ($res, $v, $k) { return "$res $v:$k"; }); //=> ' 2:baz 1:bar 0:foo'
     * </code>
     *
     * @param array $array
     * @param callable $callback ($accumulator, $value, $key) -> mixed
     * @param mixed $initial
     * @return mixed
     */
    public static function foldRightWithKey($array, $callback, $initial = null)
    {
        return self::foldWithKey(array_reverse($array, true), $callback, $initial);
    }

    /**
     * Finds the smallest element by result of $callback
     *
     * <code>
     * Arr::minBy(['tasty', 'big', 'cheeseburgers'], 'mb_strlen'); //=> 'big'
     * </code>
     *
     * @param array $array
     * @param callable $callback ($value, $key) -> number|string
     * @return mixed
     */
    public static function minBy($array, $callback)
    {
        $minResult = null;
        $minElement = null;
        foreach ($array as $element) {
            $current = $callback($element);
            if (!isset($minResult) || $current < $minResult) {
                $minResult = $current;
                $minElement = $element;
            }
        }

        return $minElement;
    }

    /**
     * Finds the largest element by result of $callback
     *
     * <code>
     * Arr::maxBy(['tasty', 'big', 'cheeseburgers'], 'mb_strlen'); //=> 'cheeseburgers'
     * </code>
     *
     * @param array $array
     * @param callable $callback ($value, $key) -> number|string
     * @return mixed
     */
    public static function maxBy($array, $callback)
    {
        $maxResult = null;
        $maxElement = null;
        foreach ($array as $element) {
            $current = $callback($element);
            if (!isset($maxResult) || $current > $maxResult) {
                $maxResult = $current;
                $maxElement = $element;
            }
        }

        return $maxElement;
    }

    /**
     * Returns the sum of all elements passed through $callback
     *
     * <code>
     * Arr::sumBy(['tasty', 'big', 'cheeseburgers'], 'mb_strlen'); // => 21
     * </code>
     *
     * @param array $array
     * @param callable $callback ($value, $key) -> number
     * @return number
     */
    public static function sumBy($array, $callback)
    {
        $sum = 0;
        foreach ($array as $value) {
            $sum += $callback($value);
        }

        return $sum;
    }


    // ----- Splitting -----

    /**
     * Returns two arrays: one with elements that satisfy the predicate, the other with elements that don't
     *
     * @param array $array
     * @param callable $predicate
     * @return array
     */
    public static function partition($array, $predicate)
    {
        $pass = array();
        $fail = array();

        foreach ($array as $key => $value) {
            $predicate($value, $key)
                ? $pass[$key] = $value
                : $fail[$key] = $value;
        }

        return array($pass, $fail);
    }

    /**
     * @param array $array
     * @param int $size
     * @param int $step
     * @return GroupedIterator
     */
    public static function sliding($array, $size, $step = 1)
    {
        return new GroupedIterator($array, $size, $step);
    }


    // ----- Zipping -----

    /**
     * Zips together two or more arrays

     * <code>
     * Arr::zip(range(1, 5), range('a', 'e'), [5, 4, 3, 2, 1]);
     * //=> [[1, a, 5], [2, b, 4], [3, c, 3], [4, d, 2], [5, e, 1]]
     * </code>
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function zip($array1, $array2)
    {
        $args = func_get_args();
        array_unshift($args, null);
        return call_user_func_array('array_map', $args);
    }

    /**
     * @param array $array1
     * @param array $array2
     * @param callable $callback
     * @return array
     */
    public static function zipWith($array1, $array2, $callback)
    {
        $result = array();
        foreach ($array1 as $a) {
            list(,$b) = each($array2);
            $result[] = $callback($a, $b);
        }

        return $result;
    }


    // ----- Sorting -----

    /**
     * Returns a copy of the array, sorted by a key or result of a callback
     *
     * @param array $array
     * @param callable|string $callbackOrKey
     * @param int $mode Sort flags
     * @return array
     */
    public static function sortBy($array, $callbackOrKey, $mode = SORT_REGULAR)
    {
        $sortBy = array();
        if (is_string($callbackOrKey)) {
            foreach ($array as $value) {
                $sortBy[] = $value[$callbackOrKey];
            }
        } else {
            foreach ($array as $key => $value) {
                $sortBy[] = $callbackOrKey($value, $key);
            }
        }

        array_multisort($sortBy, $mode, $array);

        return $array;
    }


    /**
     * @deprecated Use the appropriate non-underscored method instead
     * @param string $method
     * @param array $args
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        if ($method[0] === '_') {
            return call_user_func_array(__CLASS__.'::'.substr($method, 1), $args);
        }
        throw new \InvalidArgumentException("Method $method does not exist");
    }

}
