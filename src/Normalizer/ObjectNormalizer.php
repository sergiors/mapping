<?php

namespace Sergiors\Mapping\Normalizer;

use Doctrine\Instantiator\Instantiator;
use Sergiors\Mapping\Configuration\Metadata\ClassMetadataFactoryInterface;
use Sergiors\Mapping\Configuration\Metadata\PropertyInfoInterface;
use Sergiors\Mapping\Configuration\Annotation\Index;
use Sergiors\Mapping\Configuration\Annotation\Collection;

/**
 * @author Sérgio Rafael Siqueira <sergio@inbep.com.br>
 */
class ObjectNormalizer
{
    /**
     * @var ClassMetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var Instantiator
     */
    private $instantiator;

    /**
     * @param ClassMetadataFactoryInterface $metadataFactory
     */
    public function __construct(ClassMetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
        $this->instantiator = new Instantiator();
    }

    /**
     * @param array $attrs
     *
     * @return void|object
     */
    public function denormalize(array $attrs)
    {
        if (false === $namespace = $this->getOrThen('@namespace', $attrs, false)) {
            return;
        }

        unset($attrs['@namespace']);

        if (!class_exists($namespace)) {
            throw new ClassDoesNotExistException(sprintf('Class %s does not exist', $namespace));
        }

        $object = $this->instantiator->instantiate($namespace);
        $props = $this->metadataFactory->getMetadataForClass($namespace);

        return array_reduce($props, function ($object, PropertyInfoInterface $property) use ($attrs) {
            $reflProperty = new \ReflectionProperty($object, $property->getName());
            $reflProperty->setAccessible(true);

            if ($property->getAnnotation() instanceof Collection) {
                $nested = $this->nested(
                    $property->getAnnotation()->class,
                    $this->getOrThen($property->getDeclaringName(), $attrs, [])
                );
                $reflProperty->setValue($object, $nested);

                return $object;
            }

            if ($property->getAnnotation() instanceof Index) {
                $reflProperty->setValue($object, $this->getOrThen($property->getDeclaringName(), $attrs, null));
            }

            return $object;
        }, $object);
    }

    /**
     * @param string $namespace
     * @param array  $attrs
     *
     * @return array
     */
    private function nested($namespace, array $attrs)
    {
        return array_map(function (array $attr) use ($namespace) {
            $attr['@namespace'] = $namespace;
            return $this->denormalize($attr);
        }, $attrs);
    }

    /**
     * @param string $prop
     * @param array  $map
     * @param null   $default
     *
     * @return mixed
     */
    private function getOrThen($prop, array $map, $default = null)
    {
        if (array_key_exists($prop, $map)) {
            return $map[$prop];
        }

        return $default;
    }
}