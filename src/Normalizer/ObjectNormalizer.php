<?php

namespace Sergiors\Mapping\Normalizer;

use Doctrine\Instantiator\Instantiator;
use Sergiors\Mapping\Configuration\Metadata\ClassMetadataFactoryInterface;
use Sergiors\Mapping\Configuration\Metadata\PropertyInfoInterface;
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
     * @param array       $data
     * @param string|null $class
     *
     * @return void|object
     */
    public function denormalize(array $data, $class = null)
    {
        $class = $this->getIn('@class', $data, $class);

        if (null === $class) {
            return;
        }

        if (!class_exists($class)) {
            throw new ClassDoesNotExistException(sprintf('Class %s does not exist', $class));
        }

        $object = $this->instantiator->instantiate($class);
        $properties = $this->metadataFactory->getPropertiesForClass($class);

        return array_reduce($properties, function ($object, PropertyInfoInterface $property) use ($data) {
            $reflProperty = new \ReflectionProperty($object, $property->getName());
            $reflProperty->setAccessible(true);

            $class = $this->getIn('class', $property->getAnnotation(), false);
            $attrs = $this->getIn($property->getDeclaringName(), $data, []);

            if ($property->getAnnotation() instanceof Collection) {
                $reflProperty->setValue(
                    $object,
                    $this->nested($attrs, $class)
                );

                return $object;
            }

            if ($class) {
                $reflProperty->setValue(
                    $object,
                    $this->denormalize(array_merge($attrs, ['@class' => $class]))
                );

                return $object;
            }

            $reflProperty->setValue($object, $attrs);

            return $object;
        }, $object);
    }

    /**
     * @param string $class
     * @param array  $data
     *
     * @return array
     */
    private function nested(array $data, $class)
    {
        return array_map(function (array $attrs) use ($class) {
            return $this->denormalize($attrs, $class);
        }, $data);
    }

    /**
     * @param string     $prop
     * @param mixed      $map
     * @param mixed|null $default
     *
     * @return mixed
     */
    private function getIn($prop, $map, $default = null)
    {
        $map = (array) $map;

        if (isset($map[$prop])) {
            return $map[$prop];
        }

        return $default;
    }
}
