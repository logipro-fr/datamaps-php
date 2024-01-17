<?php

namespace DatamapsPHP\Tests;

use DatamapsPHP\DatamapsClient;
use DatamapsPHP\DTOs\Map;
use DatamapsPHP\SucceedingDatamapsClientMockFactory;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpClient\MockHttpClient;

class SucceedingDatamapsClientMockFactoryTest extends TestCase
{
    public function testGetRequest(): void
    {
        $client = SucceedingDatamapsClientMockFactory::make();
        $this->assertHttpClientInstanceOf(MockHttpClient::class, $client);

        $mapFromGet = $client->get("myMapId");
        $this->assertEquals(
            SucceedingDatamapsClientMockFactory::getExpectedGetResponse("myMapId"),
            $mapFromGet
        );
    }

    public function testSearchREquest(): void
    {
        $client = SucceedingDatamapsClientMockFactory::make();
        $this->assertHttpClientInstanceOf(MockHttpClient::class, $client);

        $mapsFromSearch = $client->search(2);
        $this->assertEquals(
            SucceedingDatamapsClientMockFactory::getExpectedSearchResponse(2),
            $mapsFromSearch
        );
    }

    public function testCreateRequest(): void
    {
        $client = SucceedingDatamapsClientMockFactory::make();
        $this->assertHttpClientInstanceOf(MockHttpClient::class, $client);

        $mapFromCreate = $client->create($mapToCreate = new Map(
            "whatever_i_put_it_will_change",
            [[42,-6],[50,10]],
            "whatever_i_put_it_will_change",
            []
        ));
        $mapFromSuccessfulCreate = SucceedingDatamapsClientMockFactory::getExpectedCreateResponse($mapToCreate);
        $this->assertNotEquals($mapFromSuccessfulCreate->mapId, $mapToCreate->mapId);

        $this->assertEquals($mapFromSuccessfulCreate->mapId, $mapFromCreate->mapId);
        $this->assertEquals($mapFromSuccessfulCreate->bounds, $mapFromCreate->bounds);
        $this->assertNotEquals($mapFromSuccessfulCreate->createdAt, $mapFromCreate->createdAt);
        $this->assertEquals($mapFromSuccessfulCreate->layers, $mapFromCreate->layers);
    }

    /** @param class-string<object> $expectedHttpClientClass */
    public function assertHttpClientInstanceOf(string $expectedHttpClientClass, DatamapsClient $client): void
    {
        $datamapsClientReflection = new ReflectionClass($client::class);
        $httpClientValue = $datamapsClientReflection->getProperty("httpClient")->getValue($client);

        $this->assertInstanceOf($expectedHttpClientClass, $httpClientValue);
    }
}
