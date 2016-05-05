<?php

namespace Sergiors\Mapping\Configuration\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class Collection implements AnnotationInterface
{
    /**
     * @var string
     */
    public $name;

    /**
     * @Required
     *
     * @var string
     */
    public $class;
}
