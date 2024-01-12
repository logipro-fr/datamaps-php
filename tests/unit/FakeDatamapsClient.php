<?php

namespace DatamapsPHP\Tests;

use Closure;
use DatamapsPHP\DatamapsClient;
use DatamapsPHP\DTOs\Map;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_decode;
use function Safe\json_encode;

class FakeDatamapsClient extends DatamapsClient
{
    private static Map $mapCreated;

    public function __construct(
        private HttpClientInterface $httpClient
    ) {
    }

    protected function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    public static function makeMock(): self
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
        return new self(new MockHttpClient($callable));
    }

    private static function responseToGet(string $url): MockResponse
    {
        Assert::assertStringStartsWith(self::BASE_URI . "display/", $url);

        $explodedUrl = explode("/", $url);
        $id = end($explodedUrl);

        if (isset(self::$mapCreated)) {
            if ($id == self::$mapCreated->mapId) {
                return new MockResponse(self::makeSuccessfulResponse((array) self::$mapCreated));
            }
        }

        return new MockResponse(self::makeSuccessfulResponse(self::makeDefaultMap($id)));
    }

    private static function responseToSearch(string $url): MockResponse
    {
        Assert::assertStringStartsWith(self::BASE_URI . "search/", $url);

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
        Assert::assertStringStartsWith(self::BASE_URI . "create", $url);
        Assert::assertArrayHasKey("body", $options);

        /** @var \stdClass $object */
        $object = json_decode($options["body"]);

        if (self::objectIsWellBuilt($object)) {
            self::$mapCreated = new Map(
                "dm_map_MapCreatedJustBefore",
                $object->bounds,
                "2024-01-01T01:01:01+00:00",
                $object->layers
            );

            return new MockResponse(
                self::makeSuccessfulResponse([
                    "mapId" => self::$mapCreated->mapId,
                    "displayUrl" => self::BASE_URI . "display/" . self::$mapCreated->mapId
                ])
            );
        } else {
            return new MockResponse(
                self::makeFailureResponse(
                    403,
                    "Error on request to Datamaps. /bounds: Array should have at least 2 items, 1 found"
                )
            );
        }
    }

    private static function objectIsWellBuilt(\stdClass $object): bool
    {
        $allValuesArePresent = isset($object->bounds) && isset($object->layers);
        if ($allValuesArePresent) {
            $bounds = $object->bounds;
            $boundsIsCorrect =
                is_array($bounds) && sizeof($bounds) == 2
                && is_array($bounds[0]) && sizeof($bounds[0]) == 2
                && is_array($bounds[1]) && sizeof($bounds[1]) == 2;
            if ($boundsIsCorrect) {
                $layers = $object->layers;
                $layersIsCorrect = is_array($layers);
                if ($layersIsCorrect) {
                    return true;
                }
            }
        }
        return false;
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
