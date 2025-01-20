<?php

namespace DatamapsPHP\Integration;

use DatamapsPHP\DatamapsClient;
use DatamapsPHP\DatamapsClientFactory;
use DatamapsPHP\Tests\DatamapsClientTest as UnitDatamapsClientTest;

class DatamapsClientTest extends UnitDatamapsClientTest
{
    protected function getClient(): DatamapsClient
    {
        return DatamapsClientFactory::make();
    }

    protected function getFailingClient(): DatamapsClient
    {
        return DatamapsClientFactory::make();
    }

    // Removed because it will never throw an error because of an empty repository
    // public function testSearchFailure(): void
    // {
    //     $this->assertTrue(true);
    // }

    // Removed to not create a real map on Datamaps
    // public function testCreate(): void
    // {
    //     $this->assertTrue(true);
    // }
}
