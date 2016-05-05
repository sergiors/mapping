<?php

namespace Sergiors\Mapping\Tests\Fixtures;

use Sergiors\Mapping\Configuration\Annotation as Mapping;

class Foo
{
    /**
     * @Mapping\Index(name="id")
     */
    public $name;

    /**
     * @Mapping\Index
     */
    public $label;

    /**
     * @Mapping\Collection(class="Sergiors\Mapping\Tests\Fixtures\Buzz")
     */
    public $buzz;
}