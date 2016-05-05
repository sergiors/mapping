<?php

namespace Sergiors\Mapping\Tests\Fixtures;

use Sergiors\Mapping\Configuration\Annotation as Mapping;

class Buzz
{
    /**
     * @Mapping\Index
     */
    public $name;
}