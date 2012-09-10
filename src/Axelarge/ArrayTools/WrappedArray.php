<?php
namespace Axelarge\ArrayTools;

/**
 * @license MIT License
 * @license www.opensource.org/licenses/MIT
 */
class WrappedArray implements ArrLike
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


    /** @inheritdoc */
    public function toArray($recursive = false)
    {
        if (!$recursive) return $this->arr;

        return array_map(function ($x) {
            return $x instanceof ArrLike ? $x->toArray(true) : $x;
        }, $this->arr);
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
        return Arr::toString($this->arr);
//        return print_r($this->arr, true);
    }

    /** @inheritdoc */
    public function dup()
    {
        return new static($this->arr);
    }


    // ----- Traversal -----

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
    public function each($callback)
    {
        foreach ($this->arr as $key => $value) {
            $callback($value, $key);
        }
        return $this;
    }

    /**
     * @deprecated Use each() with flipped argument order
     * @param callable $callback
     * @return static
     */
    public function eachPair($callback)
    {
        return $this->each(function ($v, $k) use ($callback) { $callback($k, $v); });
    }

    /** @inheritdoc */
    public function tap($callback)
    {
        $callback($this);
        return $this;
    }

    /**
     * Invokes a callback passing the underlying array as the argument, ignoring the return value.
     *
     * Useful for debugging in the middle of a chain.
     * Can also be used to modify the object, although doing so is discouraged.
     *
     * <code>
     * Arr::wrap(range(1, 10))
     *      ->filter(function ($v) { return $v % 2 != 0; })
     *      ->tap(function ($arr) { array_unshift($arr, 0); }) // Add back zero
     *      ->map(function ($v) { return $v * $v; })
     *      ->tap(function ($arr) { var_dump($arr); }) // Debug without breaking the method chain
     *      ->sum();
     * </code>
     *
     * @param callable $callback
     * @return static $this
     */
    public function tapRaw($callback)
    {
        $callback($this->arr);
        return $this;
    }


    // ----- Single element access -----

    /** @inheritdoc */
    public function get($key)
    {
        return $this->arr[$key];
    }

    /** @inheritdoc */
    public function put($key, $value)
    {
        $this->arr[$key] = $value;
        return $this;
    }

    /** @inheritdoc */
    public function getNested($keys, $default = null)
    {
        return Arr::getNested($this->arr, $keys, $default);
    }

    /** @inheritdoc */
    public function getOrElse($key, $default = null)
    {
        return array_key_exists($key, $this->arr) ? $this->arr[$key] : $default;
    }

    /** @inheritdoc */
    public function getOrPut($key, $default = null)
    {
        return Arr::getOrPut($this->arr, $key, $default);
    }

    /** @inheritdoc */
    public function getAndDelete($key, $default = null)
    {
        return Arr::getAndDelete($this->arr, $key, $default);
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


    // ----- Slicing -----

    /** @inheritdoc */
    public function take($n)
    {
        $new = $n >= 0
            ? array_slice($this->arr, 0, $n)
            : array_slice($this->arr, $n);
        return new static($new);
    }

    /** @inheritdoc */
    public function takeWhile($predicate)
    {
        return new static(Arr::takeWhile($this->arr, $predicate));
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
    public function dropWhile($predicate)
    {
        return new static(Arr::dropWhile($this->arr, $predicate));
    }


    /** @inheritdoc */
    public function slice($offset, $length = null, $preserveKeys = false)
    {
        return new static(array_slice($this->arr, $offset, $length, $preserveKeys));
    }

    /** @inheritdoc */
    public function splice($offset, $length = null, $replacement = null)
    {
        $replacement instanceof WrappedArray and $replacement = $replacement->toArray();
        $new = $this->arr;
        array_splice($new, $offset, $length, $replacement);
        return new static($new);
    }


    /** @inheritdoc */
    public function reverse($preserveKeys = false)
    {
        return new static(array_reverse($this->arr, $preserveKeys));
    }

    /** @inheritdoc */
    public function find($predicate, $default = null)
    {
        return Arr::find($this->arr, $predicate, $default);
    }

    /** @inheritdoc */
    public function findLast($predicate, $default = null)
    {
        return Arr::findLast($this->arr, $predicate, $default);
    }

    /** @inheritdoc */
    public function findKey($predicate)
    {
        return Arr::findKey($this->arr, $predicate);
    }

    /** @inheritdoc */
    public function findLastKey($predicate)
    {
        return Arr::findLastKey($this->arr, $predicate);
    }

    /** @inheritdoc */
    public function indexOf($value, $strict = true)
    {
        $key = array_search($value, $this->arr, $strict);
        return $key === false ? null : $key;
    }

    /** @inheritdoc */
    public function lastIndexOf($value, $strict = true)
    {
        return Arr::lastIndexOf($this->arr, $value, $strict);
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

    /** @inheritdoc */
    public function only($keys)
    {
        if ($keys instanceof WrappedArray) {
            $keys = $keys->toArray();
        } else if (func_num_args() > 1 || !is_array($keys)) {
            $keys = func_get_args();
        }

        return new static(array_intersect_key($this->arr, array_flip($keys)));
    }

    /** @inheritdoc */
    public function except($keys)
    {
        if ($keys instanceof WrappedArray) {
            $keys = $keys->toArray();
        } else if (func_num_args() > 1 || !is_array($keys)) {
            $keys = func_get_args();
        }

        return new static(array_diff_key($this->arr, array_flip($keys)));
    }

    /** @inheritdoc */
    public function intersection($other)
    {
        $other instanceof WrappedArray and $other = $other->toArray();
        return new static(array_intersect($this->arr, $other));
    }

    /** @inheritdoc */
    public function difference($other)
    {
        $other instanceof WrappedArray and $other = $other->toArray();
        return new static(array_diff($this->arr, $other));
    }

    /** @inheritdoc */
    public function all($predicate)
    {
        return Arr::all($this->arr, $predicate);
    }

    /** @inheritdoc */
    public function any($predicate)
    {
        return Arr::any($this->arr, $predicate);
    }

    /** @inheritdoc */
    public function one($predicate)
    {
        return Arr::exactly($this->arr, 1, $predicate);
    }

    /** @inheritdoc */
    public function none($predicate)
    {
        return Arr::exactly($this->arr, 0, $predicate);
    }

    /** @inheritdoc */
    public function exactly($n, $predicate)
    {
        return Arr::exactly($this->arr, $n, $predicate);
    }

    /** @inheritdoc */
    public function indexBy($callbackOrKey, $arrayAccess = true)
    {
        return new static(Arr::indexBy($this->arr, $callbackOrKey, $arrayAccess));
    }

    /** @inheritdoc */
    public function groupBy($callbackOrKey, $arrayAccess = true)
    {
        $groups = Arr::groupBy($this->arr, $callbackOrKey, $arrayAccess);
        foreach ($groups as &$group) {
            $group = static::wrap($group);
        }

        return new static($groups);
    }

    /** @inheritdoc */
    public function sliding($size, $step = 1)
    {
        return new ArrIterator(Arr::sliding($this->arr, $size, $step));
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
        $other instanceof WrappedArray and $other = $other->toArray();
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
        $values instanceof WrappedArray and $values = $values->toArray();
        return new static(array_combine($this->arr, $values));
    }

    /** @inheritdoc */
    public function zip($array)
    {
        $array instanceof WrappedArray and $array = $array->toArray();
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
    public function filter($predicate = null)
    {
        return $predicate === null
            ? new static(array_filter($this->arr))
            : new static(array_filter($this->arr, $predicate));
    }

    /** @inheritdoc */
    public function filterWithKey($predicate)
    {
        return new static(Arr::filterWithKey($this->arr, $predicate));
    }

    /** @inheritdoc */
    public function map($callback)
    {
        return new static(array_map($callback, $this->arr));
    }

    /** @inheritdoc */
    public function mapWithKey($callback)
    {
        return new static(Arr::mapWithKey($this->arr, $callback));
    }

    /** @inheritdoc */
    public function flatMap($callback)
    {
        return new static(Arr::flatMap($this->arr, $callback));
    }

    /** @inheritdoc */
    public function mapToAssoc($callback)
    {
        return new static(Arr::mapToAssoc($this->arr, $callback));
    }

    /** @inheritdoc */
    public function flatten()
    {
        return new static(call_user_func_array('array_merge', $this->arr));
    }

    /** @inheritdoc */
    public function pluck($valueAttribute, $keyAttribute = null, $arrayAccess = true)
    {
        return new static(Arr::pluck($this->arr, $valueAttribute, $keyAttribute, $arrayAccess));
    }

    /** @inheritdoc */
    public function fold($callback, $initial = null)
    {
        return array_reduce($this->arr, $callback, $initial);
    }

    /** @inheritdoc */
    public function foldWithKey($callback, $initial = null)
    {
        return Arr::foldWithKey($this->arr, $callback, $initial);
    }

    /** @inheritdoc */
    public function foldRight($callback, $initial = null)
    {
        return array_reduce(array_reverse($this->arr, true), $callback, $initial);
    }

    /** @inheritdoc */
    public function foldRightWithKey($callback, $initial = null)
    {
        return Arr::foldRightWithKey($this->arr, $callback, $initial);
    }

    /** @inheritdoc */
    public function partition($predicate)
    {
        list($pass, $fail) = Arr::partition($this->arr, $predicate);
        return array(new static($pass), new static($fail));
    }

    /** @inheritdoc */
    public function min($callback = null)
    {
        return $callback === null ? min($this->arr) : $this->minBy($callback);
    }

    /** @inheritdoc */
    public function max($callback = null)
    {
        return $callback === null ? max($this->arr) : $this->maxBy($callback);
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

    /** @inheritdoc */
    public function zipWith($array, $callback)
    {
        $array instanceof WrappedArray and $array = $array->toArray();
        return new static(Arr::zipWith($this->arr, $array, $callback));
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

    /** @inheritdoc */
    public function sortBy($callbackOrKey, $mode = SORT_REGULAR)
    {
        return new static(Arr::sortBy($this->arr, $callbackOrKey, $mode));
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
        return array_key_exists($offset, $this->arr);
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
