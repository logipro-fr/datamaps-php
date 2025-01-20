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
}
