<?php

namespace DatamapsPHP\Tests;

use Closure;
use DatamapsPHP\DatamapsClient;
use DatamapsPHP\DTOs\Map;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

use function Safe\json_decode;
use function Safe\json_encode;

class FakeDatamapsClient
{
    private static Map $lastMapCreated;

    public static function makeMock(): DatamapsClient
    {
        $callable = Closure::fromCallable(
            function (string $method, string $url, array $options): MockResponse {
                if ($method == "GET") {
                    if (str_contains($url, "display")) {
                        return self::responseToGet($url);
                    } else {
                        return self::responseToSearch($url);
                    }
                } else {
                    return self::responseToCreate($url, $options);
                }
            }
        );
        return new DatamapsClient(new MockHttpClient($callable));
    }

    private static function responseToGet(string $url): MockResponse
    {
        Assert::assertStringStartsWith(DatamapsClient::BASE_URI . "display/", $url);

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
        Assert::assertStringStartsWith(DatamapsClient::BASE_URI . "search/", $url);

        $explodedUrl = explode("/", $url);
        $amount = end($explodedUrl);
        $maps = [];
        for ($i = 0; $i < $amount; $i++) {
            $maps[] = self::makeDefaultMap("dm_map_" . $i);
        }

        return new MockResponse(self::makeSuccessfulResponse(["maps" => $maps]));
    }

    /** @param array<string> $options */
    private static function responseToCreate(string $url, array $options): MockResponse
    {
        Assert::assertStringStartsWith(DatamapsClient::BASE_URI . "create", $url);
        Assert::assertArrayHasKey("body", $options);

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
            "bounds" => [[42, -5], [50, 10]],
            "createdAt" => "2024-01-01T01:01:01+00:00",
            "layers" => []
        ];
    }

    public static function makeFailingMock(): DatamapsClient
    {
        $callable = Closure::fromCallable(
            function (string $method, string $url, array $options): MockResponse {
                if ($method == "GET") {
                    if (str_contains($url, "display")) {
                        return new MockResponse(
                            self::makeFailureResponse(
                                404,
                                "Error on request to Datamaps. Map with mapId 'non_existing_map' not found"
                            )
                        );
                    } else {
                        return new MockResponse(
                            self::makeFailureResponse(
                                422,
                                "Error on request to Datamaps. Can't retrieve data from an empty repository"
                            )
                        );
                    }
                } else {
                    return new MockResponse(
                        self::makeFailureResponse(
                            403,
                            "Error on request to Datamaps. /bounds: Array should have at least 2 items, 1 found"
                        )
                    );
                }
            }
        );
        return new DatamapsClient(new MockHttpClient($callable));
    }

    private static function makeFailureResponse(int $errorCode, string $message): string
    {
        return json_encode([
            "success" => false,
            "data" => [],
            "error_code" => $errorCode,
            "message" => $message
        ]);
    }
}
