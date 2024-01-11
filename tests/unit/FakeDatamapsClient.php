<?php

namespace DatamapsPHP\Tests;

use Closure;
use DatamapsPHP\DatamapsClient;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_encode;

class FakeDatamapsClient extends DatamapsClient
{
    public function __construct(
        private HttpClientInterface $httpClient
    )
    {
    }

    protected function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    public static function makeMockForGet(): self
    {
        $callable = Closure::fromCallable(
            function(string $method, string $url): MockResponse {
                Assert::assertEquals("GET", $method);
                Assert::assertStringStartsWith(self::BASE_URI."display/", $url);

                $explodedUrl = explode("/", $url);
                $id = end($explodedUrl);

                return new MockResponse(
                    json_encode([
                        "success" => true,
                        "data" => [
                            "mapId" => $id,
                            "bounds" => [[42, -5], [50, 10]],
                            "createdAt" => "2024-01-01T01:01:01+00:00",
                            "layers" => []
                        ],
                        "error_code" => 200,
                        "message" => ""
                    ])
                );
            }
        );
        return new self(new MockHttpClient($callable));
    }

    public static function makeMockForSearch(): self
    {
        $callable = Closure::fromCallable(
            function(string $method, string $url): MockResponse {
                Assert::assertEquals("GET", $method);
                Assert::assertStringStartsWith(self::BASE_URI."search/", $url);

                $explodedUrl = explode("/", $url);
                $amount = end($explodedUrl);

                $maps = [];
                for($i = 0; $i < $amount; $i++) {
                    $maps[] = [
                        "mapId" => "dm_map_".$i,
                        "bounds" => [[42, -5], [50, 10]],
                        "createdAt" => "2024-01-01T01:01:01+00:00",
                        "layers" => []
                    ];
                }

                return new MockResponse(
                    json_encode([
                        "success" => true,
                        "data" => [
                            "maps" => $maps
                        ],
                        "error_code" => 200,
                        "message" => ""
                    ])
                );
            }
        );
        return new self(new MockHttpClient($callable));
    }
}