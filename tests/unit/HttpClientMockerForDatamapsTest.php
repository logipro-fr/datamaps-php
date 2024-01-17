<?php

namespace DatamapsPHP\Tests;

use DatamapsPHP\DatamapsClient;
use DatamapsPHP\HttpClientMockerForDatamaps;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

use function Safe\json_encode;

class HttpClientMockerForDatamapsTest extends TestCase
{
    use HttpClientMockerForDatamaps;

    public function testGetRequest(): void
    {
        $httpClient = self::makeHttpClient();
        $response = $httpClient->request("GET", DatamapsClient::GET_URI . "mapIdToTestGet");
        $this->assertEquals("GET request on mapIdToTestGet", $response->getContent());
    }

    protected static function makeGetResponse(string $mapId): MockResponse
    {
        return new MockResponse("GET request on " . $mapId);
    }

    public function testSearchRequest(): void
    {
        $httpClient = self::makeHttpClient();
        $response = $httpClient->request("GET", DatamapsClient::SEARCH_URI . "2");
        $this->assertEquals("SEARCH request for 2 maps", $response->getContent());
    }

    protected static function makeSearchResponse(int $amount): MockResponse
    {
        return new MockResponse("SEARCH request for " . $amount . " maps");
    }

    public function testCreateRequest(): void
    {
        $httpClient = self::makeHttpClient();
        $response = $httpClient->request("POST", DatamapsClient::CREATE_URI, [
            "body" => json_encode((object) [
                "bounds" => [],
                "layers" => []
            ])
        ]);
        $this->assertEquals("CREATE request with bounds as [] and layers as []", $response->getContent());
    }

    protected static function makeCreateResponse(\stdClass $body): MockResponse
    {
        return new MockResponse(
            "CREATE request with bounds as ["
            . implode(",", $body->bounds)
            . "] and layers as ["
            . implode(",", $body->layers)
            . "]"
        );
    }

    public function testBadRequest(): void
    {
        $httpClient = self::makeHttpClient();
        $response = $httpClient->request("GET", "anythingelse");

        $this->assertEquals("Bad request on Datamaps", $response->getContent());
    }
}
