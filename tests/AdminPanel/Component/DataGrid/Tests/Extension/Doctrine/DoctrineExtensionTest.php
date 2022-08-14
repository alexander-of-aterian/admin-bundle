<?php

declare(strict_types=1);

namespace AdminPanel\Component\DataGrid\Tests\Extension\Doctrine;

use AdminPanel\Component\DataGrid\Extension\Doctrine\DoctrineExtension;

class DoctrineExtensionTest extends \PHPUnit\Framework\TestCase
{
    public function testLoadedTypes()
    {
        $extension = new DoctrineExtension();

        $this->assertTrue($extension->hasColumnType('entity'));
        $this->assertFalse($extension->hasColumnType('foo'));
    }
}
