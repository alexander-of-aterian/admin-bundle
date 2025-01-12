<?php

declare(strict_types=1);

namespace AdminPanel\Component\DataGrid;

use AdminPanel\Component\DataGrid\Data\DataRowsetInterface;
use AdminPanel\Component\DataGrid\Column\ColumnTypeInterface;
use AdminPanel\Component\DataGrid\Column\HeaderViewInterface;

class DataGridView implements DataGridViewInterface
{
    /**
     * Original column objects passed from datagrid.
     * This array should be used only to call methods like createCellView or
     * createHeaderView.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * @var array
     */
    protected $columnsHeaders = [];

    /**
     * Unique data grid name.
     *
     * @var string
     */
    protected $name;

    /**
     * @var \AdminPanel\Component\DataGrid\Data\DataRowsetInterface
     */
    protected $rowset;

    /**
     * Constructs DataGridView, should be called only from DataGrid::createView method.
     *
     * @param string $name
     * @param array $columns
     * @param \AdminPanel\Component\DataGrid\Data\DataRowsetInterface $rowset
     * @throws \InvalidArgumentException
     */
    public function __construct($name, array $columns = [], DataRowsetInterface $rowset)
    {
        foreach ($columns as $column) {
            if (!$column instanceof ColumnTypeInterface) {
                throw new \InvalidArgumentException('Column must implement AdminPanel\Component\DataGrid\Column\ColumnTypeInterface');
            }

            $this->columns[$column->getName()] = $column;
            $headerView = $column->createHeaderView();
            $headerView->setDataGridView($this);
            $this->columnsHeaders[$column->getName()] = $headerView;
        }

        $this->name = $name;
        $this->rowset = $rowset;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumn($name)
    {
        return array_key_exists($name, $this->columnsHeaders);
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumnType($type)
    {
        foreach ($this->columnsHeaders as $header) {
            if ($header->getType() == $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function removeColumn($name)
    {
        if (isset($this->columnsHeaders[$name])) {
            unset($this->columnsHeaders[$name]);
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumn($name)
    {
        if ($this->hasColumn($name)) {
            return $this->columnsHeaders[$name];
        }

        throw new \InvalidArgumentException(sprintf('Column "%s" does not exist in data grid.', $name));
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        return $this->columnsHeaders;
    }

    /**
     * {@inheritdoc}
     */
    public function clearColumns()
    {
        $this->columnsHeaders = [];
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addColumn(HeaderViewInterface $column)
    {
        if (!array_key_exists($column->getName(), $this->columns)) {
            throw new \InvalidArgumentException(sprintf('Column with name "%s" was never registred in datagrid ""', $column->getName(), $this->getName()));
        }

        $this->columnsHeaders[$column->getName()] = $column;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setColumns(array $columns)
    {
        $this->columnsHeaders = [];

        foreach ($columns as $column) {
            if (!$column instanceof HeaderViewInterface) {
                throw new \InvalidArgumentException('Column must implement AdminPanel\Component\DataGrid\Column\HeaderViewInterface');
            }
            if (!array_key_exists($column->getName(), $this->columns)) {
                throw new \InvalidArgumentException(sprintf('Column with name "%s" was never registred in datagrid ""', $column->getName(), $this->getName()));
            }

            $this->columnsHeaders[$column->getName()] = $column;
        }

        return $this;
    }

    /**
     * Return rowset indexes as array.
     *
     * @return array
     */
    public function getIndexes()
    {
        $indexes = [];
        foreach ($this->rowset as $index => $row) {
            $indexes[] = $index;
        }

        return $indexes;
    }

    /**
     * Returns the number of elements in the collection.
     *
     * Implementation of Countable::count()
     *
     * @return int
     */
    public function count(): int
    {
        return $this->rowset->count();
    }

    /**
     * Return the current element.
     * Similar to the current() function for arrays in PHP
     * Required by interface Iterator.
     *
     * @return \AdminPanel\Component\DataGrid\DataGridRowView current element from the rowset
     */
    public function current(): mixed
    {
        $index = $this->rowset->key();
        return new DataGridRowView($this, $this->getOriginColumns(), $this->rowset->current(), $index);
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
        return $this->rowset->key();
    }

    /**
     * Move forward to next element.
     * Similar to the next() function for arrays in PHP.
     * Required by interface Iterator.
     */
    public function next(): void
    {
        $this->rowset->next();
    }

    /**
     * Rewind the Iterator to the first element.
     * Similar to the reset() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return \AdminPanel\Component\DataGrid\DataGridViewInterface
     */
    public function rewind(): void
    {
        $this->rowset->rewind();
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
        return $this->rowset->valid();
    }

    /**
     * Check if an offset exists.
     * Required by the ArrayAccess implementation.
     *
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset): bool
    {
        return isset($this->rowset[$offset]);
    }

    /**
     * Get the row for the given offset.
     * Required by the ArrayAccess implementation.
     *
     * @param int $offset
     * @return \AdminPanel\Component\DataGrid\DataGridRowViewInterface
     * @throws \InvalidArgumentException
     */
    public function offsetGet($offset): mixed
    {
        if ($this->offsetExists($offset)) {
            return new DataGridRowView($this, $this->getOriginColumns(), $this->rowset[$offset], $offset);
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

    /**
     * Return the origin columns in order of columns headers.
     *
     * @return array
     */
    protected function getOriginColumns()
    {
        $columns = [];
        foreach ($this->columnsHeaders as $name => $header) {
            $columns[$name] = $this->columns[$name];
        }

        return $columns;
    }
}
