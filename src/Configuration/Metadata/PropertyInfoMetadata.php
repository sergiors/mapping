<?php

namespace Sergiors\Mapping\Configuration\Metadata;

use Sergiors\Mapping\Configuration\Annotation\AnnotationInterface;

/**
 * @author Sérgio Rafael Siqueira <sergio@inbep.com.br>
 */
class PropertyInfoMetadata implements PropertyInfoInterface, \Serializable
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $declaringClass;

    /**
     * @var AnnotationInterface
     */
    private $annotation;

    /**
     * @var array
     */
    private $nestedProperty = [];

    /**
     * @param string              $name
     * @param string              $declaringClass
     * @param AnnotationInterface $annotation
     * @param array               $nestedProperty
     */
    public function __construct(
        $name,
        $declaringClass,
        AnnotationInterface $annotation,
        array $nestedProperty = []
    ) {
        $this->name = $name;
        $this->annotation = $annotation;
        $this->declaringClass = $declaringClass;
        $this->nestedProperty = $nestedProperty;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeclaringName()
    {
        return $this->annotation->name ?: $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeclaringClass()
    {
        return $this->declaringClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getAnnotation()
    {
        return $this->annotation;
    }

    /**
     * {@inheritdoc}
     */
    public function getNestedProperty()
    {
        return $this->nestedProperty;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            $this->name,
            $this->declaringClass,
            $this->annotation,
            $this->nestedProperty
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list(
            $this->name,
            $this->declaringClass,
            $this->annotation,
            $this->nestedProperty
        ) = unserialize($serialized);
    }
}
