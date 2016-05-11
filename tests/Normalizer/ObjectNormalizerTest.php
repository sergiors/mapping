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
            [
                'name' => 'zzz',
                '@class' => Buzz::class
            ],
            [
                [
                    'tag' => 'foo',
                    'value' => [1, 2, 3, 4]
                ],
                [
                    'tag' => 'bar zzz'
                ],
                '@class' => Attribute::class
            ]
        ];
        $expected = $normalizer->denormalize($attrs);
        $this->assertCount(2, $expected);
        $this->assertInstanceOf(Buzz::class, $expected[0]);
        $this->assertEquals('zzz', $expected[0]->name);
        $this->assertEquals('foo', $expected[1][0]->getName());
        $this->assertEquals([1, 2, 3, 4], $expected[1][0]->getValue());

        $attrs = [
            [
                [
                    'tag' => 'foo',
                    'value' => [1, 2, 3, 4],
                    '@class' => Attribute::class
                ],
                [
                    'tag' => 'bar',
                    'value' => 'foo',
                    '@class' => Attribute::class
                ],
            ]
        ];
        $expected = $normalizer->denormalize($attrs);
        $this->assertCount(1, $expected);
        $this->assertInstanceOf(Attribute::class, $expected[0][0]);
        $this->assertInstanceOf(Attribute::class, $expected[0][1]);

        $attrs = [
            [
                'tag' => 'foo',
                'value' => [1, 2, 3, 4],
            ]
        ];
        $expected = $normalizer->denormalize($attrs, Attribute::class);
        $this->assertInstanceOf(Attribute::class, $expected[0]);
        $this->assertEquals($attrs[0]['tag'], $expected[0]->getName());
        $this->assertEquals($attrs[0]['value'], $expected[0]->getValue());
    }

    /**
     * @test
     */
    public function shouldReturnCollectionDiffObjects()
    {
        $normalizer = $this->createNormalizer();
        $attrs = [
            [
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
            ]
        ];
        $expected = $normalizer->denormalize($attrs);


        $this->assertInstanceOf(Bar::class, $expected[0]);
        $this->assertEquals(1, $expected[0]->id);
        $this->assertInstanceOf(Attribute::class, $expected[0]->foo[0]);
        $this->assertInstanceOf(Buzz::class, $expected[0]->foo[1]);
    }

    /**
     * @test
     */
    public function shouldReturnNestedObjectAnnotation()
    {
        $normalizer = $this->createNormalizer();
        $attrs = [
            [
                'buzz' => [
                    'name' => 'zzz'
                ]
            ]
        ];
        $expected = $normalizer->denormalize($attrs, Foo::class);
        $this->assertInstanceOf(Foo::class, $expected[0]);
        $this->assertInstanceOf(Buzz::class, $expected[0]->buzz);
    }

    /**
     * @test
     * @expectedException \Sergiors\Mapping\Normalizer\ClassDoesNotExistException
     */
    public function shouldThrowClassDoesNotExistException()
    {
        $normalizer = $this->createNormalizer();

        $normalizer->denormalize([
            [
                '@class' => 'Fake',
            ]
        ]);
    }

    /**
     * @test
     */
    public function shouldReturnVoid()
    {
        $normalizer = $this->createNormalizer();
        $this->assertNull($normalizer->denormalize([]));
    }

    private function createNormalizer()
    {
        $reader = new AnnotationReader();
        $driver = new AnnotationDriver($reader);
        $factory = new ClassMetadataFactory($driver);

        return new ObjectNormalizer($factory);
    }
}
