<?php

declare(strict_types=1);

namespace AdminPanel\Component\DataGrid\Tests\Extension\Core\ColumnType;

use AdminPanel\Component\DataGrid\Extension\Core\ColumnType\Text;

class TextTest extends \PHPUnit\Framework\TestCase
{
    public function testTrimOption()
    {
        $column = new Text();
        $column->initOptions();
        $column->setOption('trim', true);

        $value = [
            ' VALUE ',
        ];

        $this->assertSame(
            ['VALUE'],
            $column->filterValue($value)
        );
    }
}
