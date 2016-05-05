<?php

namespace Sergiors\Mapping\Tests\Configuration\Metadata;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\FilesystemCache;
use Sergiors\Mapping\Configuration\Metadata\Driver\AnnotationDriver;
use Sergiors\Mapping\Configuration\Metadata\ClassMetadataFactory;
use Sergiors\Mapping\Tests\Fixtures\Bar;

class ClassMetadataFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $metadata;

    private $cache;

    public function setUp()
    {
        $reader = new AnnotationReader();
        $driver = new AnnotationDriver($reader);
        $this->cache = new FilesystemCache(sys_get_temp_dir());
        $this->metadata = new ClassMetadataFactory($driver, $this->cache);
    }

    public function tearDown()
    {
        $this->cache->delete(Bar::class);
    }

    /**
     * @test
     */
    public function shouldReturnSome()
    {
        $expected = $this->metadata->getPropertiesForClass(Bar::class);
        $this->assertEquals($expected, $this->metadata->getPropertiesForClass(Bar::class));
    }
}