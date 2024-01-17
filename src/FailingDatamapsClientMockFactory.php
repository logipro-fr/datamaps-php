<?php

namespace DatamapsPHP;

use Symfony\Component\HttpClient\Response\MockResponse;

use function Safe\json_encode;

class FailingDatamapsClientMockFactory extends DatamapsClientFactory
{
    use HttpClientMockerForDatamaps;

    public const GET_ERROR_CODE = 404;
    public const GET_ERROR_MESSAGE = "Error on request to Datamaps. Map with mapId 'non_existing_map' not found";

    public const SEARCH_ERROR_CODE = 422;
    public const SEARCH_ERROR_MESSAGE = "Error on request to Datamaps. Can't retrieve data from an empty repository";

    public const CREATE_ERROR_CODE = 403;
    public const CREATE_ERROR_MESSAGE =
        "Error on request to Datamaps. /bounds: Array should have at least 2 items, 1 found";

    private static function makeGetResponse(string $mapId): MockResponse
    {
        return new MockResponse(
            self::makeDefaultFailingResponse(
                self::GET_ERROR_CODE,
                self::GET_ERROR_MESSAGE
            )
        );
    }

    private static function makeSearchResponse(int $amount): MockResponse
    {
        return new MockResponse(
            self::makeDefaultFailingResponse(
                self::SEARCH_ERROR_CODE,
                self::SEARCH_ERROR_MESSAGE
            )
        );
    }

    private static function makeCreateResponse(\stdClass $body): MockResponse
    {
        return new MockResponse(
            self::makeDefaultFailingResponse(
                self::CREATE_ERROR_CODE,
                self::CREATE_ERROR_MESSAGE
            )
        );
    }

    private static function makeDefaultFailingResponse(int $errorCode, string $message): string
    {
        return json_encode([
            "success" => false,
            "data" => [],
            "error_code" => $errorCode,
            "message" => $message
        ]);
    }
}
