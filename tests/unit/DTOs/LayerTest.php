<?php

namespace DatamapsPHP\Tests\DTOs;

use DatamapsPHP\DTOs\Layer;
use DatamapsPHP\DTOs\Marker;
use PHPUnit\Framework\TestCase;

class LayerTest extends TestCase
{
    public function testCreateFromObject(): void
    {
        $markerObj1 = (object) array(
            "point" => [0, 0],
            "description" => "first marker",
            "color" => "red"
        );
        $markerObj2 = (object) array(
            "point" => [1, 1],
            "description" => "second marker",
            "color" => "blue"
        );

        $object = (object) array(
            "name" => "My special Layer",
            "markers" => array(
                $markerObj1,
                $markerObj2
            )
        );

        $layer = Layer::createFromObject($object);

        $this->assertEquals("My special Layer", $layer->name);
        $this->assertEquals([
            Marker::createFromObject($markerObj1),
            Marker::createFromObject($markerObj2)
        ], $layer->markers);
    }
}
