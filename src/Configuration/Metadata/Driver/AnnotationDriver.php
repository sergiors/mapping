<?php

namespace Sergiors\Mapping\Configuration\Metadata\Driver;

use Doctrine\Common\Annotations\Reader;
use Sergiors\Mapping\Configuration\Annotation\Mapping;
use Sergiors\Mapping\Configuration\Metadata\PropertyInfoMetadata;

/**
 * @author Sérgio Rafael Siqueira <sergio@inbep.com.br>
 */
class AnnotationDriver implements MappingDriverInterface
{
    /**
     * @var Reader
     */
    private $readerDriver;

    /**
     * @param Reader $readerDriver
     */
    public function __construct(Reader $readerDriver)
    {
        $this->readerDriver = $readerDriver;
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertiesForClass($className)
    {
        if (!class_exists($className)) {
            return [];
        }

        $reflClass = new \ReflectionClass($className);
        $reflProperties = $reflClass->getProperties();

        return array_reduce($reflProperties, function ($properties, \ReflectionProperty $reflProperty) {
            /** @var Mapping $mapping */
            if ($mapping = $this->readerDriver->getPropertyAnnotation($reflProperty, Mapping::class)) {
                $properties[] = new PropertyInfoMetadata(
                    $reflProperty->getName(),
                    $reflProperty->getDeclaringClass()->getName(),
                    $mapping,
                    $this->getPropertiesForClass($mapping->class)
                );
            }

            return $properties;
        }, []);
    }
}
