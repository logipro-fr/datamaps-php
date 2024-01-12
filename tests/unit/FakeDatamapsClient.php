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

    public static function makeMockForGet(): self
    {
        $callable = Closure::fromCallable(
            function (string $method, string $url): MockResponse {
                Assert::assertEquals("GET", $method);
                Assert::assertStringStartsWith(self::BASE_URI . "display/", $url);

                $explodedUrl = explode("/", $url);
                $id = end($explodedUrl);

                return new MockResponse(
                    self::makeDefaultResponse(
                        self::makeDefaultMap($id)
                    )
                );
            }
        );
        return new self(new MockHttpClient($callable));
    }

    public static function makeMockForSearch(): self
    {
        $callable = Closure::fromCallable(
            function (string $method, string $url): MockResponse {
                Assert::assertEquals("GET", $method);
                Assert::assertStringStartsWith(self::BASE_URI . "search/", $url);

                $explodedUrl = explode("/", $url);
                $amount = end($explodedUrl);

                $maps = [];
                for ($i = 0; $i < $amount; $i++) {
                    $maps[] = self::makeDefaultMap("dm_map_" . $i);
                }

                return new MockResponse(
                    self::makeDefaultResponse([
                        "maps" => $maps
                    ])
                );
            }
        );
        return new self(new MockHttpClient($callable));
    }

    public static function makeMockForCreate(): self
    {
        $callable = Closure::fromCallable(
            function (string $method, string $url, array $options): MockResponse {
                if ($method === "POST")
                {
                    Assert::assertEquals("POST", $method);
                    Assert::assertStringStartsWith(self::BASE_URI . "create", $url);
                    Assert::assertArrayHasKey("body", $options);

                    $json = json_decode($options["body"]);
                    self::$mapCreated = new Map(
                        "dm_map_MapCreatedJustBefore",
                        $json->bounds,
                        "2024-01-01T01:01:01+00:00",
                        $json->layers
                    );

                    return new MockResponse(
                        self::makeDefaultResponse([
                            "mapId" => self::$mapCreated->mapId,
                            "displayUrl" => self::BASE_URI."display/".self::$mapCreated->mapId
                        ])
                    );
                } else {
                    Assert::assertEquals("GET", $method);
                    Assert::assertStringStartsWith(self::BASE_URI . "display/", $url);
                    Assert::assertStringContainsString("dm_map_MapCreatedJustBefore", $url);

                    return new MockResponse(
                        self::makeDefaultResponse(
                            (array) self::$mapCreated
                        )
                    );
                }
            }
        );
        return new self(new MockHttpClient($callable));
    }

    /** @param array<mixed> $data */
    private static function makeDefaultResponse(array $data): string
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
}
