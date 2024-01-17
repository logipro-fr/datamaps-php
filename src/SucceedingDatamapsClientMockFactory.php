<?php

namespace DatamapsPHP;

use Closure;
use DatamapsPHP\DTOs\Map;
use Safe\DateTimeImmutable;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_encode;

class SucceedingDatamapsClientMockFactory extends DatamapsClientFactory
{
    private static Map $lastMapCreated;

    public static function make(): DatamapsClient
    {
        return new DatamapsClient(self::makeHttpClientSuccessfulMock());
    }

    private static function makeHttpClientSuccessfulMock(): HttpClientInterface
    {
        $callable = Closure::fromCallable(
            function (string $method, string $url, array $options): MockResponse {
                if (str_contains($url, "datamaps/api/v1/" . "display/")) {
                    return self::responseToGet($url);
                } elseif (str_contains($url, "datamaps/api/v1/" . "search/")) {
                    return self::responseToSearch($url);
                } else {
                    return self::responseToCreate($options);
                }
            }
        );
        return new MockHttpClient($callable);
    }

    private static function responseToGet(string $url): MockResponse
    {
        $explodedUrl = explode("/", $url);
        $id = end($explodedUrl);

        if (isset(self::$lastMapCreated)) {
            if ($id == self::$lastMapCreated->mapId) {
                return new MockResponse(self::makeSuccessfulResponse((array) self::$lastMapCreated));
            }
        }

        return new MockResponse(self::makeSuccessfulResponse(self::makeDefaultMap($id)));
    }

    private static function responseToSearch(string $url): MockResponse
    {
        $explodedUrl = explode("/", $url);
        $amount = end($explodedUrl);
        $maps = [];
        for ($i = 0; $i < $amount; $i++) {
            $maps[] = self::makeDefaultMap("dm_map_" . $i);
        }

        return new MockResponse(self::makeSuccessfulResponse(["maps" => $maps]));
    }

    /** @param array<string> $options */
    private static function responseToCreate(array $options): MockResponse
    {
        /** @var \stdClass $object */
        $object = json_decode($options["body"]);

        self::$lastMapCreated = new Map(
            "dm_map_MapCreatedJustBefore",
            $object->bounds,
            "2024-01-01T01:01:01+00:00",
            $object->layers
        );

        return new MockResponse(
            self::makeSuccessfulResponse([
                "mapId" => self::$lastMapCreated->mapId,
                "displayUrl" => DatamapsClient::BASE_URI . "display/" . self::$lastMapCreated->mapId
            ])
        );
    }

    /** @param array<mixed> $data */
    private static function makeSuccessfulResponse(array $data): string
    {
        return json_encode([
            "success" => true,
            "data" => $data,
            "error_code" => 200,
            "message" => ""
        ]);
    }

    /** @return array<mixed> */
    private static function makeDefaultMap(string $id): array
    {
        return [
            "mapId" => $id,
            "bounds" => [[42,-5], [50,10]],
            "createdAt" => "2024-01-01T01:01:01+00:00",
            "layers" => []
        ];
    }

    public static function getExpectedResponseFromGet(string $mapId): Map
    {
        return Map::createFromObject((object) self::makeDefaultMap($mapId));
    }

    /** @return array<Map> */
    public static function getExpectedResponseFromSearch(int $amount): array
    {
        $maps = [];
        for ($i = 0; $i < $amount; $i++) {
            $maps[] = Map::createFromObject((object) self::makeDefaultMap("dm_map_" . $i));
        }
        return $maps;
    }

    public static function getExpectedResponseFromCreate(Map $mapToCreate): Map
    {
        return new Map(
            "dm_map_MapCreatedJustBefore",
            $mapToCreate->bounds,
            (new DateTimeImmutable())->format(DateTimeImmutable::ISO8601_EXPANDED),
            $mapToCreate->layers
        );
    }
}
