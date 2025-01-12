<?php

declare(strict_types=1);

namespace AdminPanel\Component\DataGrid;

use AdminPanel\Component\DataGrid\Column\ColumnTypeInterface;
use AdminPanel\Component\DataGrid\Exception\UnexpectedTypeException;

class DataGridRowView implements DataGridRowViewInterface
{
    /**
     * Cells views.
     *
     * @var array
     */
    protected $cellViews = [];

    /**
     * The source object for which view is created.
     *
     * @var mixed
     */
    protected $source;

    /**
     * @var int
     */
    protected $index;

    /**
     * @param DataGridViewInterface $dataGridView
     * @param array $columns
     * @param mixed $source
     * @param int $index
     * @throws UnexpectedTypeException
     */
    public function __construct(DataGridViewInterface $dataGridView, array $columns, $source, $index)
    {
        $this->source = $source;
        $this->index = $index;
        foreach ($columns as $name => $column) {
            if (!$column instanceof ColumnTypeInterface) {
                throw new UnexpectedTypeException(
                    sprintf('Column object must implement %s', ColumnTypeInterface::class)
                );
            }

            $cellView = $column->createCellView($this->source, $index);
            $cellView->setDataGridView($dataGridView);

            $this->cellViews[$name] = $cellView;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Returns the number of cells in the row.
     * Implementation of Countable::count().
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->cellViews);
    }

    /**
     * Return the current cell view.
     * Similar to the current() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return \AdminPanel\Component\DataGrid\Column\CellViewInterface current element from the rowset
     */
    public function current(): mixed
    {
        return current($this->cellViews);
    }

    /**
     * Return the identifying key of the current column.
     * Similar to the key() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return string
     */
    public function key(): mixed
    {
        return key($this->cellViews);
    }

    /**
     * Move forward to next cell view.
     * Similar to the next() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return string
     */
    public function next(): void
    {
        next($this->cellViews);
    }

    /**
     * Rewind the Iterator to the first element.
     * Similar to the reset() function for arrays in PHP.
     * Required by interface Iterator.
     */
    public function rewind(): void
    {
        reset($this->cellViews);
    }

    /**
     * Checks if current position is valid.
     * Required by the SeekableIterator implementation.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->key() !== null;
    }

    /**
     * Required by the ArrayAccess implementation.
     *
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->cellViews[$offset]);
    }

    /**
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @throws \InvalidArgumentException
     * @return \AdminPanel\Component\DataGrid\Column\ColumnTypeInterface
     */
    public function offsetGet($offset): mixed
    {
        if ($this->offsetExists($offset)) {
            return $this->cellViews[$offset];
        }

        throw new \InvalidArgumentException(sprintf('Column "%s" does not exist in row.', $offset));
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
