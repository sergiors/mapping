<?php

namespace Sergiors\Mapping\Configuration\Metadata;

/**
 * @author Sérgio Rafael Siqueira <sergio@inbep.com.br>
 */
interface PropertyInfoInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getDeclaringName();

    /**
     * @return string
     */
    public function getDeclaringClass();

    /**
     * @return \Sergiors\Mapping\Configuration\Annotation\AnnotationInterface
     */
    public function getAnnotation();

    /**
     * @return array
     */
    public function getNestedProperty();
}