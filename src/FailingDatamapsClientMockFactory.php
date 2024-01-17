<?php

namespace DatamapsPHP;

use Closure;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_encode;

class FailingDatamapsClientMockFactory extends DatamapsClientFactory
{
    public static function make(): DatamapsClient
    {
        return new DatamapsClient(self::makeHttpClientFailingMock());
    }

    private static function makeHttpClientFailingMock(): HttpClientInterface
    {
        $callable = Closure::fromCallable(
            function (string $method, string $url, array $options): MockResponse {
                if (str_contains($url, "display/")) {
                    return new MockResponse(
                        self::makeFailureResponse(
                            404,
                            "Error on request to Datamaps. Map with mapId 'non_existing_map' not found"
                        )
                    );
                } elseif (str_contains($url, "search/")) {
                    return new MockResponse(
                        self::makeFailureResponse(
                            422,
                            "Error on request to Datamaps. Can't retrieve data from an empty repository"
                        )
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
        );
        return new MockHttpClient($callable);
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

    public static function getExceptionFromFailingGet(): DatamapsRequestFailedException
    {
        return new DatamapsRequestFailedException(
            "Error on request to Datamaps. Map with mapId 'non_existing_map' not found",
            404
        );
    }

    public static function getExceptionFromFailingSearch(): DatamapsRequestFailedException
    {
        return new DatamapsRequestFailedException(
            "Error on request to Datamaps. Can't retrieve data from an empty repository",
            422
        );
    }

    public static function getExceptionFromFailingCreate(): DatamapsRequestFailedException
    {
        return new DatamapsRequestFailedException(
            "Error on request to Datamaps. /bounds: Array should have at least 2 items, 1 found",
            403
        );
    }
}
