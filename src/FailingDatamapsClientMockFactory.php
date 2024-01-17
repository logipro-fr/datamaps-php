<?php

namespace DatamapsPHP;

use Closure;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_encode;

class FailingDatamapsClientMockFactory extends DatamapsClientFactory
{
    public const GET_ERROR_CODE = 404;
    public const GET_ERROR_MESSAGE = "Error on request to Datamaps. Map with mapId 'non_existing_map' not found";

    public const SEARCH_ERROR_CODE = 422;
    public const SEARCH_ERROR_MESSAGE = "Error on request to Datamaps. Can't retrieve data from an empty repository";

    public const CREATE_ERROR_CODE = 403;
    public const CREATE_ERROR_MESSAGE =
        "Error on request to Datamaps. /bounds: Array should have at least 2 items, 1 found";

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
                            self::GET_ERROR_CODE,
                            self::GET_ERROR_MESSAGE
                        )
                    );
                } elseif (str_contains($url, "search/")) {
                    return new MockResponse(
                        self::makeFailureResponse(
                            self::SEARCH_ERROR_CODE,
                            self::SEARCH_ERROR_MESSAGE
                        )
                    );
                } else {
                    return new MockResponse(
                        self::makeFailureResponse(
                            self::CREATE_ERROR_CODE,
                            self::CREATE_ERROR_MESSAGE
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
}
