<?php

namespace Sergiors\Mapping\Tests\Fixtures;

use Sergiors\Mapping\Configuration\Annotation as Mapping;

class Product
{
    /**
     * @Mapping\Collection(class="Sergiors\Mapping\Tests\Fixtures\Attribute")
     */
    public $attributes;
}
