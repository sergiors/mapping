<?php

namespace Sergiors\Mapping\Tests\Normalizer;

use Doctrine\Common\Annotations\AnnotationReader;
use Sergiors\Mapping\Configuration\Metadata\ClassMetadataFactory;
use Sergiors\Mapping\Configuration\Metadata\Driver\AnnotationDriver;
use Sergiors\Mapping\Normalizer\ObjectNormalizer;
use Sergiors\Mapping\Tests\Fixtures\Attribute;
use Sergiors\Mapping\Tests\Fixtures\Bar;
use Sergiors\Mapping\Tests\Fixtures\Foo;
use Sergiors\Mapping\Tests\Fixtures\Buzz;

class ObjectNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnObject()
    {
        $normalizer = $this->createNormalizer();

        $attrs = [
            'tag' => 'foo',
            'value' => [1, 2, 3, 4],
            '@namespace' => Attribute::class,
        ];
        $expected = $normalizer->denormalize($attrs);

        $this->assertInstanceOf(Attribute::class, $expected);
        $this->assertEquals($attrs['tag'], $expected->getName());
        $this->assertEquals($attrs['value'], $expected->getValue());

        $attrs = [
            'uuid' => 10,
            'foo' => [
                [
                    'name' => 'foo',
                    'buzz' => [
                        [
                            'name' => 'zzz',
                        ],
                    ],
                ],
                [
                    'name' => 'bar',
                ],
            ],
            '@namespace' => Bar::class,
        ];

        $expected = $normalizer->denormalize($attrs);
        $this->assertInstanceOf(Bar::class, $expected);
        $this->assertInstanceOf(Foo::class, $expected->foo[0]);
        $this->assertInstanceOf(Buzz::class, $expected->foo[0]->buzz[0]);
        $this->assertCount(2, $expected->foo);
        $this->assertEquals(10, $expected->id);
    }

    /**
     * @test
     * @expectedException \Sergiors\Mapping\Normalizer\ClassDoesNotExistException
     */
    public function shouldThrowClassDoesNotExistException()
    {
        $normalizer = $this->createNormalizer();

        $normalizer->denormalize([
            '@namespace' => 'Fake',
        ]);
    }

    /**
     * @test
     */
    public function shouldReturnVoid()
    {
        $normalizer = $this->createNormalizer();

        $expected = $normalizer->denormalize([]);
        $this->assertEquals(null, $expected);
    }

    private function createNormalizer()
    {
        $reader = new AnnotationReader();
        $driver = new AnnotationDriver($reader);
        $factory = new ClassMetadataFactory($driver);

        return new ObjectNormalizer($factory);
    }
}
