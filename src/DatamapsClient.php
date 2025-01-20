<?php

namespace DatamapsPHP;

use DatamapsPHP\DTOs\Map;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_decode;
use function Safe\json_encode;

class DatamapsClient
{
    private HttpClientInterface $httpClient;

    public function __construct(
        ?HttpClientInterface $httpClient = null,
        private ApiUrls $apiUrls = new ApiUrls()
    ) {
        if ($httpClient == null) {
            $this->httpClient = HttpClient::create();
        } else {
            $this->httpClient = $httpClient;
        }
    }

    public function get(string $mapId): Map
    {
        /** @var \stdClass&object{layers:array<\stdClass>,mapId:string,bounds:array<array<float>>,createdAt:string} $data */
        $data = $this->queryGET($this->apiUrls->getUri() . $mapId);

        return Map::createFromObject(
            $data
        );
    }

    /** @return array<Map> */
    public function search(int $amount): array
    {
        /** @var \stdClass&object{maps:iterable<mixed>} $data */
        $data = $this->queryGET($this->apiUrls->searchUri() . $amount);

        $maps = [];
        foreach ($data->maps as $map) {
            /** @var  \stdClass&object{layers:array<\stdClass>,mapId:string,bounds:array<array<float>>,createdAt:string} $map */
            $maps[] = Map::createFromObject($map);
        }
        return $maps;
    }

    public function create(Map $map): Map
    {
        /** @var \stdClass&object{bounds:array<mixed>,layers:array<mixed>,mapId:string} */
        $data = $this->queryPOST(
            $this->apiUrls->createUri(),
            json_encode([
                "bounds" => $map->bounds,
                "layers" => $map->layers
            ])
        );

        $map = $this->get($data->mapId);

        return $map;
    }

    private function queryGET(string $uri): \stdClass
    {
        $stringifiedResponse = $this->httpClient->request('GET', $uri)->getContent();

        /** @var \stdClass&object{success:bool,data:\stdClass,message:string,error_code:int} $response */
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

    private function queryPOST(string $uri, string $data): \stdClass
    {
        $stringifiedResponse = $this->httpClient->request(
            'POST',
            $uri,
            [
                "body" => $data
            ]
        )->getContent();

        /** @var \stdClass&object{success:bool,data:\stdClass,message:string,error_code:int} $response */
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
