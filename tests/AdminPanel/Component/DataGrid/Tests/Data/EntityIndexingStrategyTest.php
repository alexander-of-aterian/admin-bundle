<?php

declare(strict_types=1);

namespace AdminPanel\Component\DataGrid\Tests\Data;

use AdminPanel\Component\DataGrid\DataMapper\DataMapperInterface;
use AdminPanel\Component\DataGrid\Tests\Fixtures\Entity;
use AdminPanel\Component\DataGrid\Data\EntityIndexingStrategy;
use AdminPanel\Component\DataGrid\Tests\Fixtures\EntityManagerMock;

class EntityIndexingStrategyTest extends \PHPUnit\Framework\TestCase
{
    protected $dataMapper;

    protected function setUp(): void
    {
        $this->dataMapper = $this->createMock(DataMapperInterface::class);
    }

    public function testInvalidObject()
    {
        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');

        $strategy = new EntityIndexingStrategy($registry);
        $this->assertSame(null, $strategy->getIndex('foo', $this->dataMapper));
    }

    public function testGetIndex()
    {
        $self = $this;

        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->once())
            ->method('getManagerForClass')
            ->will($this->returnCallback(function () use ($self) {
                $metadataFactory = $self->createMock('Doctrine\ORM\Mapping\ClassMetadataFactory');
                $metadataFactory->expects($self->once())
                    ->method('getMetadataFor')
                    ->will($self->returnCallback(function () use ($self) {
                        $classMetadata = $self->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
                                ->disableOriginalConstructor()
                                ->getMock();

                        $classMetadata->expects($self->once())
                                ->method('getIdentifierFieldNames')
                                ->will($self->returnValue(['id']));

                        return $classMetadata;
                    }));

                $em = new EntityManagerMock();
                $em->_setMetadataFactory($metadataFactory);

                return $em;
            }));

        $strategy = new \AdminPanel\Component\DataGrid\Data\EntityIndexingStrategy($registry);

        $this->dataMapper->expects($this->once())
            ->method('getData')
            ->will($this->returnValue('test'));

        $this->assertSame('test', $strategy->getIndex(new Entity('test'), $this->dataMapper));
    }

    public function testRevertIndex()
    {
        $self = $this;

        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->once())
            ->method('getManagerForClass')
            ->will($this->returnCallback(function () use ($self) {
                $metadataFactory = $self->createMock('Doctrine\ORM\Mapping\ClassMetadataFactory');
                $metadataFactory->expects($self->once())
                    ->method('getMetadataFor')
                    ->will($self->returnCallback(function () use ($self) {
                        $classMetadata = $self->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
                            ->disableOriginalConstructor()
                            ->getMock();

                        $classMetadata->expects($self->once())
                            ->method('getIdentifierFieldNames')
                            ->will($self->returnValue(['id']));

                        return $classMetadata;
                    }));

                $em = new EntityManagerMock();
                $em->_setMetadataFactory($metadataFactory);

                return $em;
            }));

        $index = 'test|id';

        $strategy = new EntityIndexingStrategy($registry);
        $this->assertSame(['id' => 'test|id'], $strategy->revertIndex($index, 'Entity'));
    }

    public function testRevertIndexComposite()
    {
        $self = $this;

        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())
            ->method('getManagerForClass')
            ->will($this->returnCallback(function () use ($self) {
                $metadataFactory = $self->createMock('Doctrine\ORM\Mapping\ClassMetadataFactory');
                $metadataFactory->expects($self->any())
                    ->method('getMetadataFor')
                    ->will($self->returnCallback(function () use ($self) {
                        $classMetadata = $self->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
                            ->disableOriginalConstructor()
                            ->getMock();

                        $classMetadata->expects($self->any())
                            ->method('getIdentifierFieldNames')
                            ->will($self->returnValue(['id', 'name']));

                        return $classMetadata;
                    }));

                $em = new EntityManagerMock();
                $em->_setMetadataFactory($metadataFactory);

                return $em;
            }));

        $strategy = new EntityIndexingStrategy($registry);
        foreach (['_', '|'] as $separator) {
            $index = '1'.$separator.'Foo';
            $strategy->setSeparator($separator);
            $this->assertSame(['id' => '1', 'name' => 'Foo' ], $strategy->revertIndex($index, 'Entity'));
        }
    }

    public function testGetIndexForNonEntities()
    {
        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $strategy = new EntityIndexingStrategy($registry);
        $this->assertSame(null, $strategy->getIndex(new \stdClass(), $this->dataMapper));
    }
}
