<?php

namespace Sergiors\Mapping\Normalizer;

use Doctrine\Instantiator\Instantiator;
use Sergiors\Mapping\Configuration\Metadata\ClassMetadataFactoryInterface;
use Sergiors\Mapping\Configuration\Metadata\PropertyInfoInterface;
use Sergiors\Mapping;
use Sergiors\Functional as F;

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
     * @param array       $attrs
     * @param string|null $class
     *
     * @return array
     */
    public function denormalize(array $attrs, $class = null)
    {
        if ([] === $attrs) {
            return;
        }

        if (!F\prop(0, $attrs)) {
            return $this->nested($attrs, F\get($attrs, '@class', $class));
        }

        return array_map(function ($attrs) use ($class) {
            if ($class = F\get($attrs, '@class', $class)) {
                unset($attrs['@class']);
            }

            if (Mapping\array_multi_exists($attrs)) {
                return $this->denormalize($attrs, $class);
            }

            return $this->nested($attrs, $class);
        }, $attrs);
    }

    /**
     * @param array  $attrs
     * @param string $class
     *
     * @return mixed
     */
    private function nested(array $attrs, $class)
    {
        if (!class_exists($class)) {
            throw new ClassDoesNotExistException(sprintf('Class %s does not exist', $class));
        }

        $object = $this->instantiator->instantiate($class);
        $props = $this->metadataFactory->getPropertiesForClass($class);

        return array_reduce($props, function ($object, PropertyInfoInterface $prop) use ($attrs) {
            $reflProperty = new \ReflectionProperty($object, $prop->getName());
            $reflProperty->setAccessible(true);

            $attrs = F\get($attrs, $prop->getDeclaringName(), null);
            $class = F\get($attrs, '@class', F\prop('class', $prop->getAnnotation()));

            if ($class && is_array($attrs)) {
                $reflProperty->setValue(
                    $object,
                    Mapping\array_multi_exists($attrs)
                        ? $this->denormalize($attrs, $class)
                        : $this->nested($attrs, $class)
                );

                return $object;
            }

            $reflProperty->setValue($object, $attrs);

            return $object;
        }, $object);
    }
}
