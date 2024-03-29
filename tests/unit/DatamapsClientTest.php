<?php

namespace DatamapsPHP\Tests;

use DatamapsPHP\DatamapsClient;
use DatamapsPHP\DatamapsRequestFailedException;
use DatamapsPHP\DTOs\Map;
use DatamapsPHP\FailingDatamapsClientMockFactory;
use DatamapsPHP\SucceedingDatamapsClientMockFactory;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DatamapsClientTest extends TestCase
{
    protected function getClient(): DatamapsClient
    {
        return SucceedingDatamapsClientMockFactory::make();
    }

    protected function getFailingClient(): DatamapsClient
    {
        return FailingDatamapsClientMockFactory::make();
    }

    public function testDefaultHttpClient(): void
    {
        $datamapsClient = new DatamapsClient();

        $datamapsClientReflection = new ReflectionClass(DatamapsClient::class);
        $httpClientValue = $datamapsClientReflection->getProperty("httpClient")->getValue($datamapsClient);

        $this->assertInstanceOf(HttpClientInterface::class, $httpClientValue);
    }

    public function testGet(): void
    {
        $map = $this->getClient()->get("dm_map_0043af51c6be58d357db18474bbf");

        $this->assertEquals("dm_map_0043af51c6be58d357db18474bbf", $map->mapId);
    }

    public function testGetFailure(): void
    {
        $this->expectException(DatamapsRequestFailedException::class);
        $this->expectExceptionMessage(
            "Error on request to Datamaps. Map with mapId 'non_existing_map' not found"
        );
        $this->expectExceptionCode(404);

        $this->getFailingClient()->get("non_existing_map");
    }

    public function testSearch(): void
    {
        $maps = $this->getClient()->search(2);

        $this->assertCount(2, $maps);
        $this->assertNotEquals($maps[0]->mapId, $maps[1]->mapId);
    }

    public function testSearchFailure(): void
    {
        $this->expectException(DatamapsRequestFailedException::class);
        $this->expectExceptionMessage(
            "Error on request to Datamaps. Can't retrieve data from an empty repository"
        );
        $this->expectExceptionCode(422);

        $this->getFailingClient()->search(1);
    }

    public function testCreate(): void
    {
        $map = new Map(
            "irrelevant_willnotbeused",
            [[42, -5], [50, 10]],
            "irrelevant_willnotbeused",
            []
        );

        $mapCreated = $this->getClient()->create($map);

        $this->assertStringStartsWith("dm_map_", $mapCreated->mapId);
        $this->assertSame([[42, -5], [50, 10]], $mapCreated->bounds);
        $this->assertSame([], $mapCreated->layers);
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

        $this->getFailingClient()->create($map);
    }
}
