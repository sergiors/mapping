<?php

namespace Sergiors\Mapping\Tests\Fixtures;

use Sergiors\Mapping\Configuration\Annotation\Mapping;

class Bar
{
    /**
     * @Mapping(name="uuid")
     */
    public $id;

    /**
     * @Mapping(class="Sergiors\Mapping\Tests\Fixtures\Foo")
     */
    public $foo;
}
