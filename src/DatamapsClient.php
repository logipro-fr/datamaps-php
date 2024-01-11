<?php

namespace DatamapsPHP;

use DatamapsPHP\DTOs\Map;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_decode;

class DatamapsClient
{
    protected const BASE_URI = "https://accidentprediction.fr/datamaps/api/v1/";

    protected function getHttpClient(): HttpClientInterface
    {
        return HttpClient::create();
    }

    public function get(string $mapId): Map
    {
        $stringifiedResponse = $this->getHttpClient()->request('GET', self::BASE_URI . "display/" . $mapId)->getContent();

        $response = json_decode($stringifiedResponse);

        return Map::createFromObject(
            $response->data
        );
    }

    /** @return array<Map> */
    public function search(int $amount): array
    {
        $stringifiedResponse = $this->getHttpClient()->request('GET', self::BASE_URI . "search/" . $amount)->getContent();

        $response = json_decode($stringifiedResponse);

        $maps = [];
        foreach($response->data->maps as $map) {
            $maps[] = Map::createFromObject($map);
        }

        return $maps;
    }
}