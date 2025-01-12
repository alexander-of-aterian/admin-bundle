<?php

declare(strict_types=1);

namespace AdminPanel\Component\DataGrid\Data;

class DataRowset implements DataRowsetInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param array|\Traversable $data
     * @throws \InvalidArgumentException
     */
    public function __construct($data)
    {
        if (!is_array($data)) {
            if (!$data instanceof \Traversable) {
                throw new \InvalidArgumentException('array or Iterator is expected as data.');
            }
        }

        foreach ($data as $id => $element) {
            $this->data[$id] = $element;
        }
    }

    /**
     * Return rowsets count.
     * Required by interface Countable.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Return the current element.
     * Similar to the current() function for arrays in PHP
     * Required by interface Iterator.
     *
     * @return \AdminPanel\Component\DataGrid\DataGridRowViewInterface current element from the rowset
     */
    public function current(): mixed
    {
        return current($this->data);
    }

    /**
     * Return the identifying key of the current element.
     * Similar to the key() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return int
     */
    public function key(): mixed
    {
        return key($this->data);
    }

    /**
     * Move forward to next element.
     * Similar to the next() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return \AdminPanel\Component\DataGrid\DataGridRowViewInterface|boolean
     */
    public function next(): void
    {
        next($this->data);
    }

    /**
     * Rewind the Iterator to the first element.
     * Similar to the reset() function for arrays in PHP.
     * Required by interface Iterator.
     */
    public function rewind(): void
    {
        reset($this->data);
    }

    /**
     * Check if there is a current element after calls to rewind() or next().
     * Used to check if we've iterated to the end of the collection.
     * Required by interface Iterator.
     *
     * @return bool False if there's nothing more to iterate over
     */
    public function valid(): bool
    {
        return $this->key() !== null;
    }

    /**
     * Check if an offset exists.
     * Required by the ArrayAccess interface.
     *
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * Get the row for the given offset.
     * Required by the ArrayAccess implementation.
     *
     * @param int $offset
     * @throws \InvalidArgumentException
     * @return \AdminPanel\Component\DataGrid\DataGridRowViewInterface
     */
    public function offsetGet($offset): mixed
    {
        if ($this->offsetExists($offset)) {
            return $this->data[$offset];
        }

        throw new \InvalidArgumentException(sprintf('Row "%s" does not exist in rowset.', $offset));
    }

    /**
     * Does nothing.
     * Required by the ArrayAccess implementation.
     *
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
    }

    /**
     * Does nothing.
     * Required by the ArrayAccess implementation.
     *
     * @param string $offset
     */
    public function offsetUnset($offset): void
    {
    }
}
