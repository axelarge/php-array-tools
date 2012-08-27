<?php
namespace Axelarge\ArrayTools;

/**
 * @license MIT License
 * @license www.opensource.org/licenses/MIT
 */
class Arr implements ArrLike
{
    /** @var array */
    protected $arr;


    public function __construct(array $array = array())
    {
        $this->arr = $array;
    }

    /** @inheritdoc */
    public static function wrap(array $array)
    {
        return new static($array);
    }

    /**
     * Short-hand for wrap()
     *
     * @see wrap()
     * @param array $array
     * @return static
     */
    public static function w(array $array)
    {
        return new static($array);
    }

    /**
     * Create a new instance from function arguments
     *
     * @return static
     */
    public static function create()
    {
        return new static(func_get_args());
    }

    /** @inheritdoc */
    public function toArray()
    {
        return $this->arr;
    }

    /**
     * Returns the underlying array
     *
     * @deprecated Use toArray() instead
     * @return array
     */
    public function raw()
    {
        return $this->arr;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return print_r($this->arr, true);
    }

    /** @inheritdoc */
    public function dup()
    {
        $dup = clone $this;
        return $dup;
    }

    /** @inheritdoc */
    public function reverse($preserveKeys = false)
    {
        return new static(array_reverse($this->arr, $preserveKeys));
    }

    /** @inheritdoc */
    public function put($key, $value)
    {
        $this->arr[$key] = $value;
        return $this;
    }

    /** @inheritdoc */
    public function get($key)
    {
        return $this->arr[$key];
    }

    /**
     * Returns the value at the given index or $default if it not present
     *
     * @param array $array
     * @param int|string $key
     * @param mixed $default
     * @return mixed
     */
    public static function _getOrElse($array, $key, $default = null)
    {
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }

    /** @inheritdoc */
    public function getOrElse($key, $default = null)
    {
        return array_key_exists($key, $this->arr) ? $this->arr[$key] : $default;
    }

    /**
     * Retrieves a nested element from an array or $default if it doesn't exist
     *
     * <code>
     * Arr::_getNested(['a' => ['b' => ['c' => 2]]], 'a.b.c'); //=> 2
     * Arr::_getNested(['a' => ['b' => ['c' => 2]]], ['a', 'b', 'c']); //=> 2
     * Arr::_getNested(['a' => 1], 'foo', 'default'); //=> 'default'
     * </code>
     *
     * @param array $array
     * @param string|array $keys The key path as either an array or a dot-separated string
     * @param mixed $default
     * @return mixed
     */
    public static function _getNested($array, $keys, $default = null)
    {
        if (is_string($keys)) {
            $keys = explode('.', $keys);
        } else if ($keys === null) {
            return $array;
        }

        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                $array = $array[$key];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /** @inheritdoc */
    public function getNested($keys, $default = null)
    {
        return static::_getNested($this->arr, $keys, $default);
    }

    /**
     * Returns the value at the given index. If not present, inserts $default and returns it
     *
     * @param array $array
     * @param int|string $key
     * @param mixed $default
     * @return mixed
     */
    public static function _getOrPut(&$array, $key, $default = null)
    {
        if (!isset($array[$key])) {
            $array[$key] = $default;
        }

        return $array[$key];
    }

    /** @inheritdoc */
    public function getOrPut($key, $default = null)
    {
        if (!isset($this->arr[$key])) {
            $this->arr[$key] = $default;
        }

        return $this->arr[$key];
    }

    /**
     * Deletes and returns a value from an array
     *
     * @param array $array
     * @param int|string $key
     * @param mixed $default
     * @return mixed
     */
    public static function _getAndDelete(&$array, $key, $default = null)
    {
        if (isset($array[$key])) {
            $result = $array[$key];
            unset($array[$key]);
            return $result;
        } else {
            return $default;
        }
    }

    /** @inheritdoc */
    public function getAndDelete($key, $default = null)
    {
        if (isset($this->arr[$key])) {
            $result = $this->arr[$key];
            unset($this->arr[$key]);
            return $result;
        } else {
            return $default;
        }
    }

    /** @inheritdoc */
    public function first()
    {
        return reset($this->arr);
    }

    /** @inheritdoc */
    public function last()
    {
        return end($this->arr);
    }

    /**
     * Returns the first value of the array satisfying the predicate or null
     *
     * @param array $array
     * @param callable $predicate
     * @return mixed|null
     */
    public static function _find($array, $predicate)
    {
        foreach ($array as $key => $value) {
            if ($predicate($value, $key)) {
                return $value;
            }
        }

        return null;
    }

    /** @inheritdoc */
    public function find($predicate)
    {
        return static::_find($this->arr, $predicate);
    }

    /**
     * Returns the key satisfying the predicate or null
     *
     * @param array $array
     * @param callable $predicate
     * @return int|null|string
     */
    public static function _findKey($array, $predicate)
    {
        foreach ($array as $key => $value) {
            if ($predicate($value, $key)){
                return $key;
            }
        }

        return null;
    }

    /** @inheritdoc */
    public function findKey($predicate)
    {
        return static::_findKey($this->arr, $predicate);
    }

    /** @inheritdoc */
    public function indexOf($value, $strict = true)
    {
        return array_search($value, $this->arr, $strict);
    }

    /** @inheritdoc */
    public function hasKey($key)
    {
        return array_key_exists($key, $this->arr);
    }

    /** @inheritdoc */
    public function hasValue($value, $strict = true)
    {
        return in_array($value, $this->arr, $strict);
    }

    /** @inheritdoc */
    public function length()
    {
        return count($this->arr);
    }

    /** @inheritdoc */
    public function isEmpty()
    {
        return empty($this->arr);
    }

    /** @inheritdoc */
    public function keys()
    {
        return new static(array_keys($this->arr));
    }

    /** @inheritdoc */
    public function values()
    {
        return new static(array_values($this->arr));
    }

    /** @inheritdoc */
    public function slice($offset, $length = null, $preserveKeys = false)
    {
        return new static(array_slice($this->arr, $offset, $length, $preserveKeys));
    }

    /** @inheritdoc */
    public function splice($offset, $length = null, $replacement = null)
    {
        $replacement instanceof Arr and $replacement = $replacement->toArray();
        $new = $this->arr;
        array_splice($new, $offset, $length, $replacement);
        return new static($new);
    }

    /** @inheritdoc */
    public function take($n)
    {
        $new = $n >= 0
            ? array_slice($this->arr, 0, $n)
            : array_slice($this->arr, $n);
        return new static($new);
    }

    /** @inheritdoc */
    public function drop($n)
    {
        $new = $n >= 0
            ? array_slice($this->arr, $n)
            : array_slice($this->arr, 0, $n);

        return new static($new);
    }

    /** @inheritdoc */
    public function unique()
    {
        return new static(array_unique($this->arr));
    }

    /** @inheritdoc */
    public function join($separator = '')
    {
        return implode($separator, $this->arr);
    }

    /** @inheritdoc */
    public function repeat($n)
    {
        $result = array();
        while ($n-- > 0) {
            foreach ($this->arr as $value) {
                $result[] = $value;
            }
        }

        return new static($result);
    }

    /** @inheritdoc */
    public function push($value)
    {
        array_push($this->arr, $value);
        return $this;
    }

    /** @inheritdoc */
    public function pop()
    {
        return array_pop($this->arr);
    }

    /** @inheritdoc */
    public function unshift($value)
    {
        array_unshift($this->arr, $value);
        return $this;
    }

    /** @inheritdoc */
    public function shift()
    {
        return array_shift($this->arr);
    }

    /**
     * Returns only those values whose keys are present in $keys
     *
     * <code>
     * Arr::_only(range('a', 'e'), [3, 4]); //=> ['d', 'e']
     * </code>
     *
     * @param array $array
     * @param array $keys
     * @return array
     */
    public static function _only($array, $keys)
    {
        return array_intersect_key($array, array_flip($keys));
    }

    /** @inheritdoc */
    public function only($keys)
    {
        if ($keys instanceof Arr) {
            $keys = $keys->toArray();
        } else if (func_num_args() > 1 || !is_array($keys)) {
            $keys = func_get_args();
        }

        return new static(array_intersect_key($this->arr, array_flip($keys)));
    }

    /**
     * Returns only those values whose keys are not present in $keys
     *
     * <code>
     * Arr::_except(range('a', 'e'), [2, 4]); //=> ['a', 'b', 'd']
     * </code>
     *
     * @param array $array
     * @param array $keys
     * @return array
     */
    public static function _except($array, $keys)
    {
        return array_diff_key($array, array_flip($keys));
    }

    /** @inheritdoc */
    public function except($keys)
    {
        if ($keys instanceof Arr) {
            $keys = $keys->toArray();
        } else if (func_num_args() > 1 || !is_array($keys)) {
            $keys = func_get_args();
        }

        return new static(array_diff_key($this->arr, array_flip($keys)));
    }

    /** @inheritdoc */
    public function intersection($other)
    {
        $other instanceof Arr and $other = $other->toArray();
        return new static(array_intersect($this->arr, $other));
    }

    /** @inheritdoc */
    public function difference($other)
    {
        $other instanceof Arr and $other = $other->toArray();
        return new static(array_diff($this->arr, $other));
    }

    /**
     * Returns true if all elements satisfy the given predicate
     *
     * @param $array
     * @param callable $predicate
     * @return bool
     */
    public static function _all($array, $predicate)
    {
        foreach ($array as $key => $value) {
            if (!$predicate($value, $key)) {
                return false;
            }
        }

        return true;
    }

    /** @inheritdoc */
    public function all($predicate)
    {
        return static::_all($this->arr, $predicate);
    }

    /**
     * Returns true if at least one element satisfies the given predicate
     *
     * @param $array
     * @param callable $predicate
     * @return bool
     */
    public static function _any($array, $predicate)
    {
        foreach ($array as $key => $value) {
            if ($predicate($value, $key)) {
                return true;
            }
        }

        return false;
    }

    /** @inheritdoc */
    public function any($predicate)
    {
        return static::_any($this->arr, $predicate);
    }

    /**
     * Returns true if exactly one element satisfies the given predicate
     *
     * @param $array
     * @param callable $predicate
     * @return bool
     */
    public static function _one($array, $predicate)
    {
        $foundOne = false;
        foreach ($array as $key => $value) {
            if ($predicate($value, $key)) {
                if ($foundOne) {
                    return false;
                } else {
                    $foundOne = true;
                }
            }
        }

        return $foundOne;
    }

    /** @inheritdoc */
    public function one($predicate)
    {
        return static::_one($this->arr, $predicate);
    }

    /**
     * Re-indexes the array by either results of the callback or a sub-key
     *
     * @param array $array
     * @param callable|string $callbackOrKey
     * @param bool $arrayAccess Whether to use array or object access when given a key name
     * @return array
     */
    public static function _indexBy($array, $callbackOrKey, $arrayAccess = true)
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

    /** @inheritdoc */
    public function indexBy($callbackOrKey, $arrayAccess = true)
    {
        return new static(static::_indexBy($this->arr, $callbackOrKey, $arrayAccess));
    }

    /**
     * Groups the array into sets key by either results of a callback or a sub-key
     *
     * @param array $array
     * @param callable|string $callbackOrKey
     * @param bool $arrayAccess Whether to use array or object access when given a key name
     * @return array
     */
    public static function _groupBy($array, $callbackOrKey, $arrayAccess = true)
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

    /** @inheritdoc */
    public function groupBy($callbackOrKey, $arrayAccess = true)
    {
        $groups = static::_groupBy($this->arr, $callbackOrKey, $arrayAccess);
        foreach ($groups as &$group) {
            $group = static::wrap($group);
        }

        return new static($groups);
    }

    public static function _sliding($array, $size, $step = 1)
    {
        return new GroupedIterator($array, $size, $step);
    }

    /** @inheritdoc */
    public function sliding($size, $step = 1)
    {
        return new ArrIterator(static::_sliding($this->arr, $size, $step));
    }


    // split_by

    /**
     * Returns $size random elements from the array or a single element if $size is null
     * Note that it differs from array_rand() in that it returns an array with a single element if $size is 1
     *
     * @param array $array
     * @param int|null $size
     * @return array
     */
    public static function _sample($array, $size = null)
    {
        return $size === null
            ? $array[array_rand($array)]
            : static::_only($array, (array)array_rand($array, $size));
    }

    /** @inheritdoc */
    public function sample($size = null)
    {
        return $size === null
            ? $this->arr[array_rand($this->arr)]
            : $this->only((array)array_rand($this->arr, $size));
    }

    /** @inheritdoc */
    public function merge($other)
    {
        $other instanceof Arr and $other = $other->toArray();
        return new static(array_merge($this->arr, $other));
    }

    /** @inheritdoc */
    public function reverseMerge($other)
    {
        $other instanceof static and $other = $other->toArray();
        return new static(array_merge($other, $this->arr));
    }

    /** @inheritdoc */
    public function combine($values)
    {
        $values instanceof Arr and $values = $values->toArray();
        return new static(array_combine($this->arr, $values));
    }

    /**
     * Zips together two or more arrays
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function _zip($array1, $array2)
    {
        $args = func_get_args();
        array_unshift($args, null);
        return call_user_func_array('array_map', $args);
    }

    /** @inheritdoc */
    public function zip($array)
    {
        $array instanceof Arr and $array = $array->toArray();
        return new static(array_map(null, $this->arr, $array));
    }

    /** @inheritdoc */
    public function flip()
    {
        return new static(array_flip($this->arr));
    }

    /** @inheritdoc */
    public function shuffle()
    {
        shuffle($this->arr);
        return $this;
    }

    /** @inheritdoc */
    public function shuffled()
    {
        return $this->dup()->shuffle();
    }

    /** @inheritdoc */
    public function chunk($size = 1, $preserveKeys = false)
    {
        $chunks = array();
        foreach (array_chunk($this->arr, $size, $preserveKeys) as $chunk) {
            $chunks[] = static::wrap($chunk);
        }
        return new static($chunks);
    }

    /** @inheritdoc */
    public function eachWithIndex($callback)
    {
        $i = 0;
        foreach ($this->arr as $item) {
            $callback($item, $i++);
        }
        return $this;
    }

    /** @inheritdoc */
    public function eachPair($callback)
    {
        foreach ($this->arr as $key => $value) {
            $callback($key, $value);
        }
        return $this;
    }

    /** @inheritdoc */
    public function filter($predicate = null)
    {
        return $predicate === null
            ? new static(array_filter($this->arr))
            : new static(array_filter($this->arr, $predicate));
    }

    /** @inheritdoc */
    public function tap($callback)
    {
        $callback($this);
        return $this;
    }

    /** @inheritdoc */
    public function tapRaw($callback)
    {
        $callback($this->arr);
        return $this;
    }

    /** @inheritdoc */
    public function map($callback)
    {
        return new static(array_map($callback, $this->arr));
    }

    /**
     * Map the array into another, applying $callback to each element and it's key.
     * If $createKeys is set to true, the callback should return an array with the key and value for the new element
     *
     * <code>
     * Arr::_mapWithKey(['a' => 1, 'b' => 2, 'c' => 3], function ($v, $k) { return [strtoupper($k), $v + 3]; }, true);
     * //=> ['A' => 4, 'B' => 5, 'C' => 6]
     * </code>
     *
     * @param array $array
     * @param callable $callback
     * @param bool $createKeys
     * @return array
     */
    public static function _mapWithKey($array, $callback, $createKeys = false)
    {
        $mapped = array();
        if ($createKeys) {
            foreach ($array as $key => $value) {
                list($newKey, $newValue) = $callback($value, $key);
                $mapped[$newKey] = $newValue;
            }
        } else {
            foreach ($array as $key => $value) {
                $mapped[$key] = $callback($value, $key);
            }
        }

        return $mapped;
    }

    /** @inheritdoc */
    public function mapWithKey($callback, $createKeys = false)
    {
        return new static(static::_mapWithKey($this->arr, $callback, $createKeys));
    }

    /**
     * Maps an array into another by applying $callback to each element and flattening the results
     *
     * <code>
     * Arr::_flatMap(['foo', 'bar baz'], function ($s) { return explode(' ', $s); });
     * //=> ['foo', 'bar', 'baz']
     * </code>
     *
     * @param array $array
     * @param callable $callback Should return an array
     * @return array array
     */
    public static function _flatMap($array, $callback)
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

    /** @inheritdoc */
    public function flatMap($callback)
    {
        return new static(static::_flatMap($this->arr, $callback));
    }

    /**
     * Flattens the array, combining elements of all sub-arrays into one array
     *
     * <code>
     * Arr::_flatten([[1, 2, 3], [4, 5]]); //=> [1, 2, 3, 4, 5]
     * </code>
     *
     * @param array $array
     * @return array
     */
    public static function _flatten($array)
    {
        return call_user_func_array('array_merge', $array);
    }

    /** @inheritdoc */
    public function flatten()
    {
        return new static(call_user_func_array('array_merge', $this->arr));
    }

    public static function _pluck($array, $valueAttribute, $keyAttribute = null, $arrayAccess = true)
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

    /** @inheritdoc */
    public function pluck($valueAttribute, $keyAttribute = null, $arrayAccess = true)
    {
        return new static(static::_pluck($this->arr, $valueAttribute, $keyAttribute, $arrayAccess));
    }

    /** @inheritdoc */
    public function fold($callback, $initial = null)
    {
        return array_reduce($this->arr, $callback, $initial);
    }

    /** @inheritdoc */
    public function foldRight($callback, $initial = null)
    {
        return array_reduce(array_reverse($this->arr), $callback, $initial);
    }

    /**
     * Returns two arrays: one with elements that satisfy the predicate, the other with elements that don't
     *
     * @param array $array
     * @param callable $predicate
     * @return array
     */
    public static function _partition($array, $predicate)
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

    /** @inheritdoc */
    public function partition($predicate)
    {
        list($pass, $fail) = static::_partition($this->arr, $predicate);
        return array(new static($pass), new static($fail));
    }

    /** @inheritdoc */
    public function min()
    {
        return min($this->arr);
    }

    /** @inheritdoc */
    public function max()
    {
        return max($this->arr);
    }

    /** @inheritdoc */
    public function minBy($callback)
    {
        $minResult = null;
        $minElement = null;
        foreach ($this->arr as $element) {
            $current = $callback($element);
            if (!isset($minResult) || $current < $minResult) {
                $minResult = $current;
                $minElement = $element;
            }
        }

        return $minElement;
    }

    /** @inheritdoc */
    public function maxBy($callback)
    {
        $maxResult = null;
        $maxElement = null;
        foreach ($this->arr as $element) {
            $current = $callback($element);
            if (!isset($maxResult) || $current > $maxResult) {
                $maxResult = $current;
                $maxElement = $element;
            }
        }

        return $maxElement;
    }

    /** @inheritdoc */
    public function sum($callback = null)
    {
        if ($callback === null) {
            return array_sum($this->arr);
        }

        $sum = 0;
        foreach ($this->arr as $value) {
            $sum += $callback($value);
        }
        return $sum;
    }

    /**
     * @param array $array1
     * @param array $array2
     * @param callable $callback
     * @return array
     */
    public static function _zipWith($array1, $array2, $callback)
    {
        $result = array();
        foreach ($array1 as $a) {
            list(,$b) = each($array2);
            $result[] = $callback($a, $b);
        }

        return $result;
    }

    /** @inheritdoc */
    public function zipWith($array, $callback)
    {
        $array instanceof Arr and $array = $array->toArray();
        return new static(static::_zipWith($this->arr, $array, $callback));
    }

    /** @inheritdoc */
    public function sort($preserveKeys = false, $mode = SORT_REGULAR)
    {
        $preserveKeys ? asort($this->arr, $mode) : sort($this->arr, $mode);
        return $this;
    }

    /** @inheritdoc */
    public function sorted($preserveKeys = false, $mode = SORT_REGULAR)
    {
        return $this->dup()->sort($preserveKeys, $mode);
    }

    /**
     * Sorts the array by a key or result of a callback
     *
     * @param array $array
     * @param callable|string $callbackOrKey
     * @param int $mode Sort flags
     * @return array
     */
    public static function _sortBy($array, $callbackOrKey, $mode = SORT_REGULAR)
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


    /** @inheritdoc */
    public function sortBy($callbackOrKey, $mode = SORT_REGULAR)
    {
        return new static(static::_sortBy($this->arr, $callbackOrKey, $mode));
    }

    /** @inheritdoc */
    public function sortedBy($callbackOrKey, $mode = SORT_REGULAR)
    {
        return $this->dup()->sortBy($callbackOrKey, $mode);
    }


    /**
     * Implementation of ArrayAccess
     *
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return array_key_exists($this->arr, $offset);
    }

    /**
     * Implementation of ArrayAccess
     *
     * @param mixed $offset
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->arr[$offset];
    }

    /**
     * Implementation of ArrayAccess
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->arr[$offset] = $value;
    }

    /**
     * Implementation of ArrayAccess
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->arr[$offset]);
    }

    /**
     * Implementation of IteratorAggregate
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->arr);
    }

}
