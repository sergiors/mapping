<?php

namespace Sergiors\Mapping\Tests\Fixtures;

use Sergiors\Mapping\Configuration\Annotation as Mapping;

class Product
{
    /**
     * @Mapping\Collection(name="attrs", class="Sergiors\Mapping\Tests\Fixtures\Attribute")
     */
    public $attributes;

    /**
     * @Mapping\Index(class="Sergiors\Mapping\Tests\Fixtures\Buzz")
     */
    public $buzz;
}
