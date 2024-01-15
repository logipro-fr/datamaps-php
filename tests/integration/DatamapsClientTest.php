<?php

namespace DatamapsPHP\Integration;

use DatamapsPHP\DatamapsClient;
use DatamapsPHP\Tests\DatamapsClientTest as UnitDatamapsClientTest;

class DatamapsClientTest extends UnitDatamapsClientTest
{
    protected function getClient(): DatamapsClient
    {
        return new DatamapsClient();
    }

    protected function getFailingClient(): DatamapsClient
    {
        return $this->getClient();
    }

    public function testSearchFailure(): void
    {
        // Removed because it will never throw an error because of an empty repository
        $this->assertTrue(true);
    }

    public function testCreate(): void
    {
        // Removed to not create a real map on Datamaps
        $this->assertTrue(true);
    }
}
