<?php

namespace Sergiors\Mapping\Tests\Configuration\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Sergiors\Mapping\Configuration\Metadata\Driver\AnnotationDriver;
use Sergiors\Mapping\Configuration\Metadata\PropertyInfoInterface;
use Sergiors\Mapping\Tests\Fixtures\Bar;

class AnnotationDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnArrayOfPropertyInfo()
    {
        $reader = new AnnotationReader();
        $driver = new AnnotationDriver($reader);

        $this->assertCount(2, $driver->loadMetadataForClass(Bar::class));
        $this->assertInstanceOf(PropertyInfoInterface::class, $driver->loadMetadataForClass(Bar::class)[0]);
        $this->assertEquals(Bar::class, $driver->loadMetadataForClass(Bar::class)[0]->getDeclaringClass());

        $this->assertCount(3, $driver->loadMetadataForClass(Bar::class)[1]->getNestedProperty());
    }
}