<?php
namespace Axelarge\ArrayTools;

use Iterator;

class ArrIterator implements Iterator
{
    private $iterator;

    public function __construct(Iterator $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     * Return the current element
     *
     * @return Arr
     */
    public function current()
    {
        return new Arr($this->iterator->current());
    }

    /**
     * Move forward to next element
     */
    public function next()
    {
        $this->iterator->next();
    }

    /**
     * Return the key of the current element
     *
     * @return mixed
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind()
    {
        $this->iterator->rewind();
    }
}
