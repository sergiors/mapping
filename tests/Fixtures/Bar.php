<?php

namespace Sergiors\Mapping\Tests\Fixtures;

use Sergiors\Mapping\Configuration\Annotation as Mapping;

class Bar
{
    /**
     * @Mapping\Index(name="uuid")
     */
    public $id;

    /**
     * @Mapping\Collection(class="Sergiors\Mapping\Tests\Fixtures\Foo")
     */
    public $foo;
}
