<?php

namespace DatamapsPHP;

use DatamapsPHP\DTOs\Map;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_decode;
use function Safe\json_encode;

class DatamapsClient
{
    public const BASE_URI = "https://accidentprediction.fr/datamaps/api/v1/";

    private HttpClientInterface $httpClient;

    public function __construct(
        ?HttpClientInterface $httpClient = null
    ) {
        if ($httpClient == null) {
            $this->httpClient = HttpClient::create();
        } else {
            $this->httpClient = $httpClient;
        }
    }

    public function get(string $mapId): Map
    {
        $data = $this->queryGET("display/" . $mapId);

        return Map::createFromObject(
            $data
        );
    }

    /** @return array<Map> */
    public function search(int $amount): array
    {
        $data = $this->queryGET("search/" . $amount);

        $maps = [];
        foreach ($data->maps as $map) {
            $maps[] = Map::createFromObject($map);
        }
        return $maps;
    }

    public function create(Map $map): Map
    {
        $data = $this->queryPOST(
            "create",
            json_encode([
                "bounds" => $map->bounds,
                "layers" => $map->layers
            ])
        );

        $map = $this->get($data->mapId);

        return $map;
    }

    private function queryGET(string $uriMethod): \stdClass
    {
        $stringifiedResponse = $this->httpClient->request('GET', self::BASE_URI . $uriMethod)->getContent();

        /** @var \stdClass $response */
        $response = json_decode($stringifiedResponse);

        return $response->data;
    }

    private function queryPOST(string $uriMethod, string $data): \stdClass
    {
        $stringifiedResponse = $this->httpClient->request(
            'POST',
            self::BASE_URI . $uriMethod,
            [
                "body" => $data
            ]
        )->getContent();

        /** @var \stdClass $response */
        $response = json_decode($stringifiedResponse);

        if ($response->success === true) {
            return $response->data;
        } else {
            throw new DatamapsRequestFailedException(
                sprintf("Error on request to Datamaps. %s", $response->message),
                $response->error_code
            );
        }
    }
}
