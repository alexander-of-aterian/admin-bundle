<?php

declare(strict_types=1);

namespace AdminPanel\Component\DataGrid\Tests\Extension\Core;

use AdminPanel\Component\DataGrid\Extension\Core\CoreExtension;
use AdminPanel\Component\DataGrid\Extension\Core\EventSubscriber\ColumnOrder;

class CoreExtensionTest extends \PHPUnit\Framework\TestCase
{
    public function testLoadedTypes()
    {
        $extension = new CoreExtension();
        $this->assertTrue($extension->hasColumnType('text'));
        $this->assertTrue($extension->hasColumnType('number'));
        $this->assertTrue($extension->hasColumnType('datetime'));
        $this->assertTrue($extension->hasColumnType('money'));

        $this->assertFalse($extension->hasColumnType('foo'));
    }

    public function testLoadedExtensions()
    {
        $extension = new CoreExtension();
        $this->assertTrue($extension->hasColumnTypeExtensions('text'));
        $this->assertTrue($extension->hasColumnTypeExtensions('text'));
        $this->assertTrue($extension->hasColumnTypeExtensions('number'));
        $this->assertTrue($extension->hasColumnTypeExtensions('datetime'));
        $this->assertTrue($extension->hasColumnTypeExtensions('money'));
        $this->assertTrue($extension->hasColumnTypeExtensions('gedmo_tree'));
        $this->assertTrue($extension->hasColumnTypeExtensions('entity'));
    }

    public function testColumnOrder()
    {
        $subscriber = new ColumnOrder();

        $cases = [
            [
                'columns' => [
                    'negative2' => -2,
                    'neutral1' => null,
                    'negative1' => -1,
                    'neutral2' => null,
                    'positive1' => 1,
                    'neutral3' => null,
                    'positive2' => 2,
                ],
                'sorted' => [
                    'negative2',
                    'negative1',
                    'neutral1',
                    'neutral2',
                    'neutral3',
                    'positive1',
                    'positive2',
                ]
            ],
            [
                'columns' => [
                    'neutral1' => null,
                    'neutral2' => null,
                    'neutral3' => null,
                    'neutral4' => null,
                ],
                'sorted' => [
                    'neutral1',
                    'neutral2',
                    'neutral3',
                    'neutral4',
                ]
            ]
        ];

        foreach ($cases as $case) {
            $columns = [];

            foreach ($case['columns'] as $name => $order) {
                $columnHeader = $this->createMock('AdminPanel\Component\DataGrid\Column\HeaderViewInterface');

                $columnHeader
                    ->expects($this->atLeastOnce())
                    ->method('getName')
                    ->will($this->returnValue($name));

                $columnHeader
                    ->expects($this->atLeastOnce())
                    ->method('hasAttribute')
                    ->will($this->returnCallback(function ($attribute) use ($order) {
                        if (($attribute == 'display_order') && isset($order)) {
                            return true;
                        } else {
                            return false;
                        }
                    }));

                $columnHeader
                    ->expects($this->any())
                    ->method('getAttribute')
                    ->will($this->returnCallback(function ($attribute) use ($order) {
                        if (($attribute == 'display_order') && isset($order)) {
                            return $order;
                        } else {
                            return null;
                        }
                    }));

                $columns[] = $columnHeader;
            }

            $view = $this->createMock('AdminPanel\Component\DataGrid\DataGridViewInterface');

            $self = $this;

            $view
                ->expects($this->once())
                ->method('getColumns')
                ->will($this->returnValue($columns));

            $view
                ->expects($this->once())
                ->method('setColumns')
                ->will($this->returnCallback(function (array $columns) use ($self, $case) {
                    $sorted = [];
                    foreach ($columns as $column) {
                        $sorted[] = $column->getName();
                    }
                    $self->assertSame($case['sorted'], $sorted);
                }));

            $event = $this->createMock('AdminPanel\Component\DataGrid\DataGridEventInterface');
            $event
                ->expects($this->once())
                ->method('getData')
                ->will($this->returnValue($view));

            $subscriber->postBuildView($event);
        }
    }
}
