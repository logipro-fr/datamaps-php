<?php

namespace DatamapsPHP\Tests;

use DatamapsPHP\ApiUrls;
use PHPUnit\Framework\TestCase;

class ApiUrlsTest extends TestCase
{
    public function testGetUri(): void
    {
        $apiUrls = new ApiUrls();
        $this->assertEquals("https://accidentprediction.fr/datamaps/api/v1/display/", $apiUrls->getUri());
    }

    public function testSearchUri(): void
    {
        $this->assertEquals("https://accidentprediction.fr/datamaps/api/v1/search/", (new ApiUrls())->searchUri());
    }

    public function testCreateUri(): void
    {
        $this->assertEquals("https://accidentprediction.fr/datamaps/api/v1/create", (new ApiUrls())->createUri());
    }
}
