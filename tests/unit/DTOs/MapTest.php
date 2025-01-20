<?php

namespace DatamapsPHP\Tests\DTOs;

use DatamapsPHP\DTOs\Layer;
use DatamapsPHP\DTOs\Map;
use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{
    public function testCreate(): void
    {
        $map = new Map(
            "My own Map",
            [[-1, 2], [2, 1]],
            "2023-11-15T16:18:12",
            [ new Layer("My own Layer", []) ]
        );

        $this->assertEquals("My own Map", $map->mapId);
        $this->assertEquals([[-1, 2], [2, 1]], $map->bounds);
        $this->assertEquals("2023-11-15T16:18:12", $map->createdAt);
        $this->assertEquals([ new Layer("My own Layer", []) ], $map->layers);
    }

    public function testCreateFromObject(): void
    {
        $object = (object) array(
            "mapId" => "My special Map",
            "bounds" => [[-1, -2], [2, 1]],
            "createdAt" => "2023-11-15T16:18:12",
            "layers" => [ $this->createDefaultLayerAsObject() ]
        );

        $map = Map::createFromObject($object);

        $this->assertEquals("My special Map", $map->mapId);
        $this->assertEquals([[-1, -2], [2, 1]], $map->bounds);
        $this->assertEquals("2023-11-15T16:18:12", $map->createdAt);
        $this->assertEquals([
            Layer::createFromObject($this->createDefaultLayerAsObject())
        ], $map->layers);
    }

    /** @return \stdClass&object{markers:array<object{point:array{int,int},description:string,color:string}>, name:string} */
    private function createDefaultLayerAsObject(): \stdClass
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

        $layerObj = (object) array(
            "name" => "My special Layer",
            "markers" => array(
                $markerObj1,
                $markerObj2
            )
        );

        return $layerObj;
    }
}
