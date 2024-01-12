<?php

namespace DatamapsPHP\Tests;

use DatamapsPHP\DatamapsClient;
use DatamapsPHP\DatamapsRequestFailedException;
use DatamapsPHP\DTOs\Map;
use PHPUnit\Framework\TestCase;

class DatamapsClientTest extends TestCase
{
    protected DatamapsClient $client;

    public function setUp(): void
    {
        $this->client = FakeDatamapsClient::makeMock();
    }

    public function testGet(): void
    {
        $map = $this->client->get("dm_map_0043af51c6be58d357db18474bbf");

        $this->assertEquals("dm_map_0043af51c6be58d357db18474bbf", $map->mapId);
    }

    public function testSearch(): void
    {
        $maps = $this->client->search(2);

        $this->assertCount(2, $maps);
        $this->assertNotEquals($maps[0]->mapId, $maps[1]->mapId);
    }

    public function testCreateFailure(): void
    {
        $this->expectException(DatamapsRequestFailedException::class);
        $this->expectExceptionMessage(
            "Error on request to Datamaps. /bounds: Array should have at least 2 items, 1 found"
        );
        $this->expectExceptionCode(403);

        $map = new Map(
            "irrelevant_willnotbeused",
            [[42, -5]],
            "irrelevant_willnotbeused",
            []
        );

        $this->client->create($map);
    }

    public function testCreate(): void
    {
        $map = new Map(
            "irrelevant_willnotbeused",
            [[42, -5], [50, 10]],
            "irrelevant_willnotbeused",
            []
        );

        $mapCreated = $this->client->create($map);

        $this->assertStringStartsWith("dm_map_", $mapCreated->mapId);
        $this->assertSame([[42, -5], [50, 10]], $mapCreated->bounds);
        $this->assertSame([], $mapCreated->layers);
    }
}
