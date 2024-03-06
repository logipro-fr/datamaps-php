<?php

namespace DatamapsPHP;

class ApiUrls
{
    private const BASE_URI_DEFAULT = "https://accidentprediction.fr/datamaps/api/v1/";
    private const GET_URI =  "display/";
    private const SEARCH_URI = "search/";
    private const CREATE_URI = "create";

    public function __construct(
        private string $baseUri = self::BASE_URI_DEFAULT
    ) {
    }

    public function getUri(): string
    {
        return $this->baseUri . self::GET_URI;
    }

    public function searchUri(): string
    {
        return $this->baseUri . self::SEARCH_URI;
    }

    public function createUri(): string
    {
        return $this->baseUri . self::CREATE_URI;
    }
}
