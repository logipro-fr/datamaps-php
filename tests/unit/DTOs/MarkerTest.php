<?php

namespace DatamapsPHP\Tests\DTOs;

use DatamapsPHP\DTOs\Marker;
use PHPUnit\Framework\TestCase;

class MarkerTest extends TestCase
{
    public function testCreate(): void
    {
        $marker = new Marker([2, 2.5], "My own description", "red");

        $this->assertEquals([2, 2.5], $marker->point);
        $this->assertEquals("My own description", $marker->description);
        $this->assertEquals("red", $marker->color);
    }

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
