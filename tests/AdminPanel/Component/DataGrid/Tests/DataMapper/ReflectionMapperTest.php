<?php

declare(strict_types=1);

namespace AdminPanel\Component\DataGrid\Tests\DataMapper;

use AdminPanel\Component\DataGrid\DataMapper\ReflectionMapper;
use AdminPanel\Component\DataGrid\Tests\Fixtures\EntityMapper;

class ReflectionMapperTest extends \PHPUnit\Framework\TestCase
{
    public function testGetter()
    {
        $mapper = new ReflectionMapper();
        $entity = new EntityMapper();
        $entity->setName('fooname');

        $this->assertSame('fooname', $mapper->getData('name', $entity));
    }

    public function testProtectedGetter()
    {
        $mapper = new \AdminPanel\Component\DataGrid\DataMapper\ReflectionMapper();
        $entity = new EntityMapper();
        $entity->setSurname('foosurname');

        $this->expectException('AdminPanel\Component\DataGrid\Exception\DataMappingException');
        $mapper->getData('surname', $entity);
    }

    public function testHaser()
    {
        $mapper = new \AdminPanel\Component\DataGrid\DataMapper\ReflectionMapper();
        $entity = new EntityMapper();
        $entity->setCollection('collection');

        $this->assertTrue($mapper->getData('collection', $entity));
    }

    public function testProtectedHaser()
    {
        $mapper = new ReflectionMapper();
        $entity = new EntityMapper();
        $entity->setPrivateCollection('collection');

        $this->setExpectedException('AdminPanel\Component\DataGrid\Exception\DataMappingException');
        $mapper->getData('private_collection', $entity);
    }

    public function testIser()
    {
        $mapper = new \AdminPanel\Component\DataGrid\DataMapper\ReflectionMapper();
        $entity = new EntityMapper();
        $entity->setReady(true);

        $this->assertTrue($mapper->getData('ready', $entity));
    }

    public function testProtectedIser()
    {
        $mapper = new ReflectionMapper();
        $entity = new EntityMapper();
        $entity->setProtectedReady(true);

        $this->setExpectedException('AdminPanel\Component\DataGrid\Exception\DataMappingException');
        $mapper->getData('protected_ready', $entity);
    }

    public function testProperty()
    {
        $mapper = new ReflectionMapper();
        $entity = new EntityMapper();
        $entity->setId('bar');

        $this->assertSame('bar', $mapper->getData('id', $entity));
    }

    public function testPrivateProperty()
    {
        $mapper = new \AdminPanel\Component\DataGrid\DataMapper\ReflectionMapper();
        $entity = new EntityMapper();
        $entity->setPrivateId('bar');

        $this->setExpectedException('AdminPanel\Component\DataGrid\Exception\DataMappingException');
        $mapper->getData('private_id', $entity);
    }

    public function testSetter()
    {
        $mapper = new ReflectionMapper();
        $entity = new EntityMapper();

        $mapper->setData('name', $entity, 'fooname');
        $this->assertSame('fooname', $entity->getName());
    }

    public function testProtectedSetter()
    {
        $mapper = new \AdminPanel\Component\DataGrid\DataMapper\ReflectionMapper();
        $entity = new EntityMapper();

        $this->setExpectedException('AdminPanel\Component\DataGrid\Exception\DataMappingException');
        $mapper->setData('protected_name', $entity, 'fooname');
    }

    public function testAdder()
    {
        $mapper = new ReflectionMapper();
        $entity = new EntityMapper();

        $mapper->setData('tag', $entity, 'bar');
        $this->assertSame(['bar'], $entity->getTags());
    }

    public function testProtectedAdder()
    {
        $mapper = new ReflectionMapper();
        $entity = new EntityMapper();

        $this->setExpectedException('AdminPanel\Component\DataGrid\Exception\DataMappingException');
        $mapper->setData('protected_tag', $entity, 'bar');
    }
}
