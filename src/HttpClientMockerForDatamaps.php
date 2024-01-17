<?php

namespace DatamapsPHP;

use Closure;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_decode;

trait HttpClientMockerForDatamaps
{
    public static function make(): DatamapsClient
    {
        return new DatamapsClient(self::makeHttpClient());
    }

    private static function makeHttpClient(): HttpClientInterface
    {
        $callable = Closure::fromCallable(
            function (string $method, string $url, array $options): MockResponse {
                if ($method == "GET" && str_starts_with($url, DatamapsClient::GET_URI)) {
                    $explodedUrl = explode("/", $url);
                    $mapId = end($explodedUrl);
                    return self::makeGetResponse($mapId);
                } elseif ($method == "GET" && str_starts_with($url, DatamapsClient::SEARCH_URI)) {
                    $explodedUrl = explode("/", $url);
                    $amount = end($explodedUrl);
                    return self::makeSearchResponse(intval($amount));
                } elseif ($method == "POST" && str_starts_with($url, DatamapsClient::CREATE_URI)) {
                    /** @var \stdClass $body */
                    $body = json_decode($options["body"]);
                    return self::makeCreateResponse($body);
                } else {
                    return new MockResponse("Bad request on Datamaps");
                }
            }
        );
        return new MockHttpClient($callable);
    }

    abstract private static function makeGetResponse(string $mapId): MockResponse;
    abstract private static function makeSearchResponse(int $amount): MockResponse;
    abstract private static function makeCreateResponse(\stdClass $body): MockResponse;
}
