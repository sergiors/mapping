<?php

namespace Sergiors\Mapping\Tests\Fixtures;

use Sergiors\Mapping\Configuration\Annotation as Mapping;

class Attribute
{
    /**
     * @Mapping\Index(name="tag")
     */
    private $name;

    /**
     * @Mapping\Index
     */
    private $value;

    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }
}
