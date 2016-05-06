<?php

namespace Sergiors\Mapping\Normalizer;

use Doctrine\Instantiator\Instantiator;
use Sergiors\Mapping\Configuration\Metadata\ClassMetadataFactoryInterface;
use Sergiors\Mapping\Configuration\Metadata\PropertyInfoInterface;
use Sergiors\Mapping\Configuration\Annotation\Collection;
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
     * @param array       $data
     * @param string|null $class
     *
     * @return void|object
     */
    public function denormalize(array $data, $class = null)
    {
        if (null === $class = F\get($data, '@class', $class)) {
            return;
        }

        if (!class_exists($class)) {
            throw new ClassDoesNotExistException(sprintf('Class %s does not exist', $class));
        }

        $object = $this->instantiator->instantiate($class);
        $props = $this->metadataFactory->getPropertiesForClass($class);
        $attrsFn = F\curry(function ($attrs, $name, $default) {
            return F\get($attrs, $name, $default);
        }, $data);

        return array_reduce($props, function ($object, PropertyInfoInterface $prop) use ($attrsFn) {
            $reflProperty = new \ReflectionProperty($object, $prop->getName());
            $reflProperty->setAccessible(true);

            $class = F\prop('class', $prop->getAnnotation());
            $attrs = $attrsFn($prop->getDeclaringName());

            if ($prop->getAnnotation() instanceof Collection) {
                $reflProperty->setValue(
                    $object,
                    $this->nested($attrs([]), $class)
                );

                return $object;
            }

            if ($class) {
                $reflProperty->setValue(
                    $object,
                    $this->denormalize(array_merge($attrs([]), ['@class' => $class]))
                );

                return $object;
            }

            $reflProperty->setValue($object, $attrs(null));

            return $object;
        }, $object);
    }

    /**
     * @param string $class
     * @param mixed  $data
     *
     * @return array
     */
    private function nested(array $data, $class)
    {
        return array_map(function (array $attrs) use ($class) {
            return $this->denormalize($attrs, $class);
        }, $data);
    }
}
