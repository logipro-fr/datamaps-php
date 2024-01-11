<?php

namespace DatamapsPHP\Tests\DTOs;

use DatamapsPHP\DTOs\Marker;
use PHPUnit\Framework\TestCase;

class MarkerTest extends TestCase
{
    public function testCreateFromObject(): void
    {
        $object = (object) array(
            "point" => [2, 2.5],
            "description" => "My special description",
            "color" => "red"
        );

        $marker = Marker::createFromObject($object);

        $this->assertEquals([2, 2.5], $marker->point);
        $this->assertEquals("My special description", $marker->description);
        $this->assertEquals("red", $marker->color);
    }
}
