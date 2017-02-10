<?php

declare(strict_types=1);

namespace AdminPanel\Symfony\AdminBundle\Tests\Doubles\Admin;

use AdminPanel\Symfony\AdminBundle\Admin\CRUD\DataGridAwareInterface;
use AdminPanel\Symfony\AdminBundle\Admin\CRUD\DataSourceAwareInterface;
use FSi\Component\DataGrid\DataGridFactoryInterface;
use FSi\Component\DataSource\DataSourceFactoryInterface;

class CRUDElement extends SimpleAdminElement implements DataGridAwareInterface, DataSourceAwareInterface
{
    private $dataGridFactory;
    private $dataSourceFactory;

    /**
     * @param \FSi\Component\DataGrid\DataGridFactoryInterface $factory
     */
    public function setDataGridFactory(DataGridFactoryInterface $factory)
    {
        $this->dataGridFactory = $factory;
    }

    /**
     * @param \FSi\Component\DataSource\DataSourceFactoryInterface $factory
     */
    public function setDataSourceFactory(DataSourceFactoryInterface $factory)
    {
        $this->dataSourceFactory = $factory;
    }

    public function isDataGridAware()
    {
        return isset($this->dataGridFactory);
    }

    public function isDataSourceAware()
    {
        return isset($this->dataSourceFactory);
    }
}
