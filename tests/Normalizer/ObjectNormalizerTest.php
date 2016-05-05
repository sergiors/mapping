<?php

namespace Sergiors\Mapping\Tests\Normalizer;

use Doctrine\Common\Annotations\AnnotationReader;
use Sergiors\Mapping\Configuration\Metadata\ClassMetadataFactory;
use Sergiors\Mapping\Configuration\Metadata\Driver\AnnotationDriver;
use Sergiors\Mapping\Normalizer\ObjectNormalizer;
use Sergiors\Mapping\Tests\Fixtures\Attribute;
use Sergiors\Mapping\Tests\Fixtures\Bar;

class ArrayConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnObject()
    {
        $reader = new AnnotationReader();
        $driver = new AnnotationDriver($reader);
        $factory = new ClassMetadataFactory($driver);
        $normalizer = new ObjectNormalizer($factory);

        $attrs = [
            'tag' => 'foo',
            'value' => [1, 2, 3, 4],
            '@namespace' => Attribute::class
        ];
        $expected = $normalizer->denormalize($attrs);

        $this->assertInstanceOf(Attribute::class, $expected);

        $attrs = [
            'id' => 10,
            'foo' => [
                [
                    'name' => 'foo',
                    'buzz' => [
                        [
                            'name' => 'zzz'
                        ]
                    ]
                ],
                [
                    'name' => 'bar'
                ]
            ],
            '@namespace' => Bar::class
        ];

        $expected = $normalizer->denormalize($attrs);
        $this->assertInstanceOf(Bar::class, $expected);
    }
}