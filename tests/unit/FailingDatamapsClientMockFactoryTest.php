<?php

namespace DatamapsPHP\Tests;

use DatamapsPHP\DTOs\Map;
use DatamapsPHP\FailingDatamapsClientMockFactory;
use PHPUnit\Framework\TestCase;

class FailingDatamapsClientMockFactoryTest extends TestCase
{
    public function testFailingMockCreationForGet(): void
    {
        $this->expectExceptionObject(FailingDatamapsClientMockFactory::getExceptionFromFailingGet());

        $client = FailingDatamapsClientMockFactory::make();
        $client->get("id");
    }

    public function testFailingMockCreationForSearch(): void
    {
        $this->expectExceptionObject(FailingDatamapsClientMockFactory::getExceptionFromFailingSearch());

        $client = FailingDatamapsClientMockFactory::make();
        $client->search(2);
    }

    public function testFailingMockCreationForCreate(): void
    {
        $this->expectExceptionObject(FailingDatamapsClientMockFactory::getExceptionFromFailingCreate());

        $client = FailingDatamapsClientMockFactory::make();
        $client->create(new Map("", [], "", []));
    }
}
