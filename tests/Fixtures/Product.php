<?php

namespace Sergiors\Mapping\Tests\Fixtures;

use Sergiors\Mapping\Configuration\Annotation\Mapping;

class Product
{
    /**
     * @Mapping(name="attrs", class="Sergiors\Mapping\Tests\Fixtures\Attribute")
     */
    public $attributes;

    /**
     * @Mapping(class="Sergiors\Mapping\Tests\Fixtures\Buzz")
     */
    public $buzz;
}
