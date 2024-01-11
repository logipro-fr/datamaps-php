<?php

namespace DatamapsPHP\Tests;

use DatamapsPHP\DatamapsClient;
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
}