<?php

namespace Sergiors\Mapping\Tests\Fixtures;

use Sergiors\Mapping\Configuration\Annotation\Index;

class Attribute
{
    /**
     * @Index(name="tag")
     */
    private $name;

    /**
     * @Index
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