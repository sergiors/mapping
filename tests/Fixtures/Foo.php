<?php

namespace Sergiors\Mapping\Tests\Fixtures;

use Sergiors\Mapping\Configuration\Annotation\Mapping;

class Foo
{
    /**
     * @Mapping(name="id")
     */
    public $name;

    /**
     * @Mapping
     */
    public $label;

    /**
     * @Mapping(class="Sergiors\Mapping\Tests\Fixtures\Buzz")
     */
    public $buzz;
}
