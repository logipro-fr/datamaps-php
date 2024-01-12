<?php

namespace DatamapsPHP\Integration;

use DatamapsPHP\DatamapsClient;
use DatamapsPHP\Tests\DatamapsClientTest as UnitDatamapsClientTest;

class DatamapsClientTest extends UnitDatamapsClientTest
{
    public function setUp(): void
    {
        $this->client = new DatamapsClient();
    }

    public function testCreate(): void
    {
        // Removed to not create a real map on Datamaps
        $this->assertTrue(true);
    }
}
