<?php

namespace DatamapsPHP;

use DatamapsPHP\DTOs\Map;
use Safe\DateTimeImmutable;
use Symfony\Component\HttpClient\Response\MockResponse;

use function Safe\json_encode;

class SucceedingDatamapsClientMockFactory extends DatamapsClientFactory
{
    use HttpClientMockerForDatamaps;

    public const DEFAULT_BOUNDS = [[42, -5], [50, 10]];
    public const DEFAULT_DATE = "2024-01-01T01:01:01+00:00";
    public const DEFAULT_LAYERS = [];

    private static Map $lastMapCreated;

    private static function makeGetResponse(string $mapId): MockResponse
    {
        if (isset(self::$lastMapCreated)) {
            if ($mapId == self::$lastMapCreated->mapId) {
                return new MockResponse(self::makeSuccessfulResponse((array) self::$lastMapCreated));
            }
        }

        return new MockResponse(self::makeSuccessfulResponse(self::makeDefaultMap($mapId)));
    }

    private static function makeSearchResponse(int $amount): MockResponse
    {
        $maps = [];
        for ($i = 0; $i < $amount; $i++) {
            $maps[] = self::makeDefaultMap("dm_map_" . $i);
        }

        return new MockResponse(self::makeSuccessfulResponse(["maps" => $maps]));
    }

    private static function makeCreateResponse(\stdClass $body): MockResponse
    {
        self::$lastMapCreated = new Map(
            "dm_map_MapCreatedJustBefore",
            $body->bounds,
            "2024-01-01T01:01:01+00:00",
            $body->layers
        );

        return new MockResponse(
            self::makeSuccessfulResponse([
                "mapId" => self::$lastMapCreated->mapId,
                "displayUrl" => DatamapsClient::GET_URI . self::$lastMapCreated->mapId
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
            "bounds" => self::DEFAULT_BOUNDS,
            "createdAt" => self::DEFAULT_DATE,
            "layers" => self::DEFAULT_LAYERS
        ];
    }

    public static function getExpectedResponseFromGet(string $mapId): Map
    {
        return new Map(
            $mapId,
            self::DEFAULT_BOUNDS,
            self::DEFAULT_DATE,
            self::DEFAULT_LAYERS
        );
    }

    /** @return array<Map> */
    public static function getExpectedResponseFromSearch(int $amount): array
    {
        $maps = [];
        for ($i = 0; $i < $amount; $i++) {
            $maps[] = self::getExpectedResponseFromGet("dm_map_" . $i);
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
