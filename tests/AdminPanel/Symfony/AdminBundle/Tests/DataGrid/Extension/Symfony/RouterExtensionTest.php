<?php

declare(strict_types=1);

namespace AdminPanel\Symfony\AdminBundleBundle\Tests\DataGrid\Extension\Symfony;

use AdminPanel\Symfony\AdminBundle\DataGrid\Extension\Symfony\RouterExtension;

class RouterExtensionTest extends \PHPUnit\Framework\TestCase
{
    public function testSymfonyExtension()
    {
        $router = $this->createMock('Symfony\Component\Routing\RouterInterface');
        $requestStack = $this->createMock('Symfony\Component\HttpFoundation\RequestStack');
        $extension = new RouterExtension($router, $requestStack);

        $this->assertTrue($extension->hasColumnType('action'));
    }
}
