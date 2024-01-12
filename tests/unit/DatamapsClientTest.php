<?php

namespace DatamapsPHP\Tests;

use DatamapsPHP\DatamapsClient;
use DatamapsPHP\DatamapsRequestFailedException;
use DatamapsPHP\DTOs\Map;
use PHPUnit\Framework\TestCase;

class DatamapsClientTest extends TestCase
{
    public function testGet(): void
    {
        $datamapsClient = new DatamapsClient();
        $map = $datamapsClient->get("dm_map_0043af51c6be58d357db18474bbf");

        $this->assertEquals("dm_map_0043af51c6be58d357db18474bbf", $map->mapId);
    }

    public function testFakeGet(): void
    {
        $datamapsClient = FakeDatamapsClient::makeMockForGet();
        $map = $datamapsClient->get("dm_map_0043af51c6be58d357db18474bbf");

        $this->assertEquals("dm_map_0043af51c6be58d357db18474bbf", $map->mapId);
    }

    public function testSearch(): void
    {
        $datamapsClient = new DatamapsClient();
        $maps = $datamapsClient->search(2);

        $this->assertCount(2, $maps);
        $this->assertNotEquals($maps[0]->mapId, $maps[1]->mapId);
    }

    public function testFakeSearch(): void
    {
        $datamapsClient = FakeDatamapsClient::makeMockForSearch();
        $maps = $datamapsClient->search(2);

        $this->assertCount(2, $maps);
        $this->assertNotEquals($maps[0]->mapId, $maps[1]->mapId);
    }

    public function testCreate(): void
    {
        $this->expectException(DatamapsRequestFailedException::class);
        $this->expectExceptionMessage("Error on request to Datamaps. /bounds: Array should have at least 2 items, 1 found");
        $this->expectExceptionCode(403);

        $map = new Map(
            "irrelevant_willnotbeused",
            [[42, -5]],
            "irrelevant_willnotbeused",
            []
        );

        $datamapsClient = new DatamapsClient();
        $mapCreated = $datamapsClient->create($map);
    }

    public function testFakeCreate(): void
    {
        $map = new Map(
            "irrelevant_willnotbeused",
            [[42, -5], [50, 10]],
            "irrelevant_willnotbeused",
            []
        );

        $datamapsClient = FakeDatamapsClient::makeMockForCreate();
        $mapCreated = $datamapsClient->create($map);

        $this->assertStringStartsWith("dm_map_", $mapCreated->mapId);
        $this->assertSame([[42, -5], [50, 10]], $mapCreated->bounds);
        $this->assertSame([], $mapCreated->layers);
    }
}
