<?php
namespace Axelarge\ArrayTools;

use Iterator;

class GroupedIterator implements Iterator
{
    /** @var array */
    private $array;
    /** @var int */
    private $length;
    /** @var int */
    private $chunkSize;
    /** @var int */
    private $step;
    /** @var int */
    private $current = 0;


    /**
     * @param array $array
     * @param int $chunkSize
     * @param int $step
     */
    public function __construct($array, $chunkSize, $step = 1)
    {
        $this->array = $array;
        $this->chunkSize = $chunkSize;
        $this->step = $step;
        $this->length = count($array);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return iterator_to_array($this);
    }

    /**
     * @return array
     */
    public function current()
    {
        return array_slice($this->array, $this->current * $this->step, $this->chunkSize, false);
    }

    /**
     * Move forward to next element
     */
    public function next()
    {
        ++$this->current;
    }

    /**
     * Return the key of the current element
     *
     * @return int
     */
    public function key()
    {
        return $this->current;
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean true on success or false on failure.
     */
    public function valid()
    {
        return ($this->current - 1) * $this->step + $this->chunkSize < $this->length;
    }

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind()
    {
        $this->current = 0;
    }
}
