<?php

namespace Sergiors\Mapping\Tests\Normalizer;

use Doctrine\Common\Annotations\AnnotationReader;
use Sergiors\Mapping\Configuration\Metadata\ClassMetadataFactory;
use Sergiors\Mapping\Configuration\Metadata\Driver\AnnotationDriver;
use Sergiors\Mapping\Normalizer\ObjectNormalizer;
use Sergiors\Mapping\Tests\Fixtures\Product;
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
            'buzz' => ['name' =>  'zzz'],
            'attrs' => [
                [
                    'tag' => 'foo',
                    'value' => [1, 2, 3, 4]
                ],
                [
                    'tag' => 'bar'
                ]
            ]
        ];
        $expected = $normalizer->denormalize($attrs, Product::class);
        $this->assertInstanceOf(Product::class, $expected);
        $this->assertCount(2, $expected->attributes);
        $this->assertInstanceOf(Attribute::class, $expected->attributes[0]);
        $this->assertInstanceOf(Buzz::class, $expected->buzz);
        $this->assertEquals('foo', $expected->attributes[0]->getName());
        $this->assertEquals('bar', $expected->attributes[1]->getName());
        $this->assertEquals('zzz', $expected->buzz->name);

        $attrs = [
            'tag' => 'foo',
            'value' => [1, 2, 3, 4],
            '@class' => Attribute::class,
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
                        ]
                    ]
                ],
                [
                    'name' => 'bar',
                ]
            ]
        ];

        $expected = $normalizer->denormalize($attrs, Bar::class);
        $this->assertInstanceOf(Bar::class, $expected);
        $this->assertInstanceOf(Foo::class, $expected->foo[0]);
        $this->assertInstanceOf(Buzz::class, $expected->foo[0]->buzz[0]);
        $this->assertCount(2, $expected->foo);
        $this->assertEquals(10, $expected->id);
    }

    /**
     * @test
     */
    public function shouldReturnCollectionDiffObjects()
    {
        $normalizer = $this->createNormalizer();
        $attrs = [
            'uuid' => 1,
            'foo' => [
                [
                    'tag' => 'baz',
                    'value' => 'baz x',
                    '@class' => Attribute::class
                ],
                [
                    'name' => 'buzz',
                    '@class' => Buzz::class
                ]
            ],
            '@class' => Bar::class
        ];
        $bar = $normalizer->denormalize($attrs);

        $this->assertInstanceOf(Bar::class, $bar);
        $this->assertInstanceOf(Attribute::class, $bar->foo[0]);
        $this->assertInstanceOf(Buzz::class, $bar->foo[1]);
    }

    /**
     * @test
     */
    public function shouldReturnObjectWithNullProps()
    {
        $normalizer = $this->createNormalizer();

        $expected = $normalizer->denormalize([], Product::class);
        $this->assertInstanceOf(Buzz::class, $expected->buzz);
        $this->assertNull($expected->buzz->name);
    }

    /**
     * @test
     * @expectedException \Sergiors\Mapping\Normalizer\ClassDoesNotExistException
     */
    public function shouldThrowClassDoesNotExistException()
    {
        $normalizer = $this->createNormalizer();

        $normalizer->denormalize([
            '@class' => 'Fake',
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
