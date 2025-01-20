<?php

namespace DatamapsPHP;

use Closure;
use DatamapsPHP\DTOs\Layer;
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
                if (str_starts_with($url, (new ApiUrls())->getUri())) {
                    $explodedUrl = explode("/", $url);
                    $mapId = end($explodedUrl);
                    return self::makeGetResponse($mapId);
                } elseif (str_starts_with($url, (new ApiUrls())->searchUri())) {
                    $explodedUrl = explode("/", $url);
                    $amount = end($explodedUrl);
                    return self::makeSearchResponse(intval($amount));
                } elseif (str_starts_with($url, (new ApiUrls())->createUri())) {
                    /** @var array<string,string> $options */
                    /** @var object{bounds:array<array<float>>,layers:array<Layer>}&\stdClass $body */
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
