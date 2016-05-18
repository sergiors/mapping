<?php

namespace Sergiors\Mapping\Tests;

use Sergiors\Mapping;

class ArrayMultiExistsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnTrue()
    {
        $ls = [
            [
                'name' => 'James'
            ],
            [
                'name' => 'Lars'
            ]
        ];
        
        $this->assertTrue(Mapping\array_multi_exists($ls));

        $band = [
            'metallica' => [
                [
                    'name' => 'James',
                    'instrument' => 'Vocal'
                ],
                [
                    'name' => 'Lars',
                    'instruments' => 'Drum'
                ]
            ]
        ];
        $this->assertTrue(Mapping\array_multi_exists($band));

        $this->assertTrue(Mapping\array_multi_exists([[]]));
    }

    /**
     * @test
     */
    public function shouldReturnFalse()
    {
        $ls = [
            'name' => 'James',
            'instrument' => 'Vocal'
        ];

        $this->assertFalse(Mapping\array_multi_exists($ls));
        $this->assertFalse(Mapping\array_multi_exists([]));
    }
}
