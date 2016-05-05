<?php

namespace Sergiors\Mapping\Configuration\Metadata\Driver;

use Doctrine\Common\Annotations\Reader;
use Sergiors\Mapping\Configuration\Annotation\Index;
use Sergiors\Mapping\Configuration\Annotation\Collection;
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
        $reflClass = new \ReflectionClass($className);
        $reflProperties = $reflClass->getProperties();

        return array_reduce($reflProperties, function ($properties, \ReflectionProperty $reflProperty) {
            /** @var Index $annotation */
            if ($annotation = $this->readerDriver->getPropertyAnnotation($reflProperty, Index::class)) {
                $properties[] = new PropertyInfoMetadata(
                    $reflProperty->getName(),
                    $reflProperty->getDeclaringClass()->getName(),
                    $annotation
                );
            }

            /** @var Collection $annotation */
            if ($annotation = $this->readerDriver->getPropertyAnnotation($reflProperty, Collection::class)) {
                $properties[] = new PropertyInfoMetadata(
                    $reflProperty->getName(),
                    $reflProperty->getDeclaringClass()->getName(),
                    $annotation,
                    $this->getPropertiesForClass($annotation->class)
                );
            }

            return $properties;
        }, []);
    }
}
