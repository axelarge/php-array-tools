<?php
namespace Axelarge\ArrayTools;

/**
 * @license MIT License
 * @license www.opensource.org/licenses/MIT
 */
class Arr implements \ArrayAccess
{
    /** @var array */
    protected $arr;

    public function __construct(array $arr = array())
    {
        $this->arr = $arr;
    }

    public static function wrap(array $arr)
    {
        return new static($arr);
    }

    /**
     * Short-hand for wrap()
     *
     * <code>
     * use Arr as A;
     * A::w([1, 2, 3])
     * </code>
     *
     * @see wrap()
     * @param array $arr
     * @return static
     */
    public static function w(array $arr)
    {
        return new static($arr);
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

    /**
     * Returns the underlying array
     *
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

    /**
     * Returns a clone of the object
     *
     * @return static
     */
    public function dup()
    {
        $dup = clone $this;
        return $dup;
    }

    /**
     * Reverses the array
     *
     * @param bool $preserveKeys
     * @return static
     */
    public function reverse($preserveKeys = false)
    {
        $this->arr = array_reverse($this->arr, $preserveKeys);
        return $this;
    }

    public function put($key, $value)
    {
        $this->arr[$key] = $value;
        return $this;
    }

    public function get($key)
    {
        return $this->arr[$key];
    }

    public function getOrElse($key, $default = null)
    {
        return isset($this->arr[$key]) ? $this->arr[$key] : $default;
    }

    public function getOrPut($key, $default = null)
    {
        if (!isset($this->arr[$key])) {
            $this->arr[$key] = $default;
        }

        return $this->arr[$key];
    }

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

    /**
     * Returns the first value
     *
     * @return mixed
     */
    public function first()
    {
        return reset($this->arr);
    }

    /**
     * Returns the first value satisfying the predicate or null
     *
     * @param \callable $predicate
     * @return mixed|null
     */
    public function find($predicate)
    {
        foreach ($this->arr as $key => $value) {
            if ($predicate($value, $key)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Returns the last value
     *
     * @return mixed
     */
    public function last()
    {
        return end($this->arr);
    }

    /**
     * Checks if the key exists in the array
     *
     * @param int|string $key
     * @return bool
     */
    public function hasKey($key)
    {
        return array_key_exists($key, $this->arr);
    }

    /**
     * Checks if the value exists in the array
     *
     * @param mixed $value
     * @param bool $strict Whether to use strict comparison for determining equality
     * @return bool
     */
    public function hasValue($value, $strict = true)
    {
        return in_array($value, $this->arr, $strict);
    }

    /**
     * Returns the length of the array
     *
     * @return int
     */
    public function length()
    {
        return count($this->arr);
    }

    /**
     * Checks if the array is empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->arr);
    }

    /**
     * Returns the keys of the array
     *
     * @return static
     */
    public function keys()
    {
        $this->arr = array_keys($this->arr);
        return $this;
    }

    /**
     * Returns the values of the array
     *
     * @return static
     */
    public function values()
    {
        $this->arr = array_values($this->arr);
        return $this;
    }

    /**
     * Returns a slice of the array
     *
     * @param int $offset
     * @param int $length
     * @param bool $preserveKeys
     * @return static
     */
    public function slice($offset, $length = null, $preserveKeys = false)
    {
        $this->arr = array_slice($this->arr, $offset, $length, $preserveKeys);
        return $this;
    }

    /**
     * @param int $offset
     * @param int $length
     * @param array|Arr $replacement
     * @return static
     */
    public function splice($offset, $length = null, $replacement = null)
    {
        $replacement instanceof Arr and $replacement = $replacement->raw();
        array_splice($this->arr, $offset, $length, $replacement);
        return $this;
    }

    // fromTo (like ruby's [3..5])

    /**
     * Returns the first $n elements or the last -$n elements if $n is negative
     *
     * @param int $n
     * @return static
     */
    public function take($n)
    {
        $this->arr = $n >= 0
            ? array_slice($this->arr, 0, $n)
            : array_slice($this->arr, $n);
        return $this;
    }

    /**
     * Returns all but the first $n elements or all but the last -$n elements if $n is negative
     *
     * @param int $n
     * @return static
     */
    public function drop($n)
    {
        $this->arr = $n >= 0
            ? array_slice($this->arr, $n)
            : array_slice($this->arr, 0, $n);

        return $this;
    }

    public function unique()
    {
        $this->arr = array_unique($this->arr);
        return $this;
    }

    /**
     * Joins the array values into a string, separated by $separator
     *
     * @param string $separator
     * @return string
     */
    public function join($separator = '')
    {
        return implode($separator, $this->arr);
    }

    public function repeat($n)
    {
        $result = array();
        while ($n-- > 0) {
            foreach ($this->arr as $value) {
                $result[] = $value;
            }
        }
        $this->arr = $result;
        return $this;
    }

    /**
     * Appends an element to the end of the array
     *
     * @param mixed $value
     * @return static
     */
    public function push($value)
    {
        array_push($this->arr, $value);
        return $this;
    }

    /**
     * Removes the last element of the array and returns it
     *
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->arr);
    }

    /**
     * Prepends an element to the front of the array
     *
     * @param mixed $value
     * @return static
     */
    public function unshift($value)
    {
        array_unshift($this->arr, $value);
        return $this;
    }

    /**
     * Removes the first element of the array and returns it
     *
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->arr);
    }

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
    public function only($keys)
    {
        if ($keys instanceof Arr) {
            $keys = $keys->raw();
        } else if (func_num_args() > 1 || !is_array($keys)) {
            $keys = func_get_args();
        }

        $this->arr = array_intersect_key($this->arr, array_flip($keys));
        return $this;
    }

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
    public function except($keys)
    {
        if ($keys instanceof Arr) {
            $keys = $keys->raw();
        } else if (func_num_args() > 1 || !is_array($keys)) {
            $keys = func_get_args();
        }

        $this->arr = array_diff_key($this->arr, array_flip($keys));
        return $this;
    }

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
    public function intersection($other)
    {
        $other instanceof Arr and $other = $other->raw();
        $this->arr = array_intersect($this->arr, $other);
        return $this;
    }

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
    public function difference($other)
    {
        $other instanceof Arr and $other = $other->raw();
        $this->arr = array_diff($this->arr, $other);
        return $this;
    }

    /**
     * Returns true if all elements satisfy the given predicate
     *
     * @param \callable $predicate
     * @return bool
     */
    public function all($predicate)
    {
        foreach ($this->arr as $key => $value) {
            if (!$predicate($value, $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if at least one element satisfies the given predicate
     *
     * @param \callable $predicate
     * @return bool
     */
    public function any($predicate)
    {
        foreach ($this->arr as $key => $value) {
            if ($predicate($value, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if exactly one element satisfies the given predicate
     *
     * @param \callable $predicate
     * @return bool
     */
    public function one($predicate)
    {
        $foundOne = false;
        foreach ($this->arr as $key => $value) {
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

    /**
     * Re-indexes the array by either results of the callback or a sub-key
     *
     * @param callback|string $callbackOrKey
     * @param bool $arrayAccess Whether to use array or object access when given a key name
     * @return static
     */
    public function indexBy($callbackOrKey, $arrayAccess = true)
    {
        $indexed = array();

        if (is_string($callbackOrKey)) {
            if ($arrayAccess) {
                foreach ($this->arr as $element) {
                    $indexed[$element[$callbackOrKey]] = $element;
                }
            } else {
                foreach ($this->arr as $element) {
                    $indexed[$element->{$callbackOrKey}] = $element;
                }
            }
        } else {
            foreach ($this->arr as $element) {
                $indexed[$callbackOrKey($element)] = $element;
            }
        }

        $this->arr = $indexed;

        return $this;
    }

    /**
     * @param callback|string $callbackOrKey
     * @param bool $arrayAccess Whether to use array or object access when given a key name
     * @return static
     */
    public function groupBy($callbackOrKey, $arrayAccess = true)
    {
        $groups = array();

        if (is_string($callbackOrKey)) {
            if ($arrayAccess) {
                foreach ($this->arr as $element) {
                    $groups[$element[$callbackOrKey]][] = $element;
                }
            } else {
                foreach ($this->arr as $element) {
                    $groups[$element->{$callbackOrKey}][] = $element;
                }
            }
        } else {
            foreach ($this->arr as $element) {
                $groups[$callbackOrKey($element)][] = $element;
            }
        }

        foreach ($groups as &$group) {
            $group = static::wrap($group);
        }

        $this->arr = $groups;

        return $this;
    }


    // split_by

    public function sample($size = 1)
    {
        return $size === 1
            ? $this->arr[array_rand($this->arr)]
            : $this->only(array_rand($this->arr, $size));
    }

    /**
     * @param array|Arr $arr
     * @return static
     */
    public function merge($arr)
    {
        $arr instanceof Arr and $arr = $arr->raw();
        $this->arr = array_merge($this->arr, $arr);
        return $this;
    }

    /**
     * @param array|static $other
     * @return static
     */
    public function reverseMerge($other)
    {
        $other instanceof static and $other = $other->raw();
        $this->arr = array_merge($other, $this->arr);
        return $this;
    }

    /**
     * @param array|Arr $arr
     * @return static
     */
    public function combine($arr)
    {
        $arr instanceof Arr and $arr = $arr->raw();
        $this->arr = array_combine($this->arr, $arr);
        return $this;
    }

    /**
     * @param array|Arr $arr
     * @return static
     */
    public function zip($arr)
    {
        $arr instanceof Arr and $arr = $arr->raw();

        $this->arr = array_map(null, $this->arr, $arr);
        return $this;
    }

    public function flip()
    {
        $this->arr = array_flip($this->arr);
        return $this;
    }

    public function shuffle()
    {
        shuffle($this->arr);
        return $this;
    }

    public function chunk($size = 1, $preserveKeys = false)
    {
        $chunks = array();
        foreach (array_chunk($this->arr, $size, $preserveKeys) as $chunk) {
            $chunks[] = static::wrap($chunk);
        }
        $this->arr = $chunks;
        return $this;
    }


    public function each($callback)
    {
        $i = 0;
        foreach ($this->arr as $item) {
            $callback($item, $i++);
        }
        return $this;
    }

    public function eachPair($callback)
    {
        foreach ($this->arr as $key => $value) {
            $callback($key, $value);
        }
        return $this;
    }

    // TODO Keys ?
    public function filter($callback = null)
    {
        $this->arr = array_filter($this->arr, $callback);
        return $this;
    }

    public function map($callback, $createKeys = false)
    {
        if ($createKeys) {
            $result = array();
            foreach (array_map($callback, $this->arr) as $pair) {
                list($key, $value) = $pair;
                $result[$key] = $value;
            }
            $this->arr = $result;
        } else {
            $this->arr = array_map($callback, $this->arr);
        }

        return $this;
    }

    public function mapWithKey($callback, $createKeys = false)
    {
        $mapped = array();
        if ($createKeys) {
            foreach ($this->arr as $key => $value) {
                list($newKey, $newValue) = $callback($value, $key);
                $mapped[$newKey] = $newValue;
            }
        } else {
            foreach ($this->arr as $key => $value) {
                $mapped[$key] = $callback($value, $key);
            }
        }

        $this->arr = $mapped;

        return $this;
    }

    public function flatMap($callback)
    {
        $result = array();
        foreach ($this->arr as $key => $value) {
            $newValues = $callback($value, $key);
            if ($newValues) {
                foreach ($newValues as $newValue) {
                    $result[] = $newValue;
                }
            }
        }
        $this->arr = $result;

        return $this;
    }

    public function flatten()
    {
        $this->arr = call_user_func_array('array_merge', $this->arr);

        return $this;
    }

    public function pluck($valueAttribute, $keyAttribute = null, $arrayAccess = true)
    {
        $result = array();
        if ($arrayAccess) {
            foreach ($this->arr as $key => $value) {
                $result[$keyAttribute ? $value[$keyAttribute] : $key] = $value[$valueAttribute];
            }
        } else {
            foreach ($this->arr as $key => $value) {
                $result[$keyAttribute ? $value->{$keyAttribute} : $key] = $value->{$valueAttribute};
            }
        }
        $this->arr = $result;

        return $this;
    }

    public function fold($callback, $initial = null)
    {
        $this->arr = array_reduce($this->arr, $callback, $initial);
        return $this;
    }

    public function foldRight($callback, $initial = null)
    {
        $this->arr = array_reduce(array_reverse($this->arr), $callback, $initial);
        return $this;
    }

    /**
     * @param $predicate
     * @return static[]
     */
    public function partition($predicate)
    {
        $pass = array();
        $fail = array();

        foreach ($this->arr as $key => $value) {
            $predicate($value, $key)
                ? $pass[$key] = $value
                : $fail[$key] = $value;
        }

        return array(new static($pass), new static($fail));
    }

    /**
     * Finds the smallest element
     *
     * @return mixed
     */
    public function min()
    {
        return min($this->arr);
    }

    /**
     * Finds the largest element
     *
     * @return mixed
     */
    public function max()
    {
        return max($this->arr);
    }

    /**
     * Finds the element for which the result of the callback is the smallest
     *
     * @param \callable $callback
     * @return mixed
     */
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

    /**
     * Finds the element for which the result of the callback is the largest
     *
     * @param \callable $callback
     * @return mixed
     */
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

    /**
     * Returns the sum of all elements
     *
     * @param \callable $callback If given, sums the results of this callback over each element
     * @return number
     */
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
     * @param array|Arr $arr
     * @param $callback
     * @return static
     */
    public function zipWith($arr, $callback)
    {
        $arr instanceof Arr and $arr = $arr->raw();

        $result = array();
        foreach ($this->arr as $a) {
            $b = each($arr);
            $result[] = $callback($a, $b);
        }
        $this->arr = $result;

        return $this;
    }


    public function sort()
    {
        // TODO
    }

    // TODO
    public function sortBy($callback, $mode = SORT_REGULAR)
    {
        $sortBy = array();
        foreach ($this->arr as $element) {
            $sortBy[] = $callback($element);
        }
        array_multisort($this->arr, $mode, $sortBy);

        return $this;
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
}
