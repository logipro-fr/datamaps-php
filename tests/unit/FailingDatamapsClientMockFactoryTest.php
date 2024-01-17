<?php

namespace DatamapsPHP\Tests;

use DatamapsPHP\DatamapsRequestFailedException;
use DatamapsPHP\DTOs\Map;
use DatamapsPHP\FailingDatamapsClientMockFactory;
use PHPUnit\Framework\TestCase;

class FailingDatamapsClientMockFactoryTest extends TestCase
{
    public function testGetRequest(): void
    {
        $this->expectException(DatamapsRequestFailedException::class);
        $this->expectExceptionCode(FailingDatamapsClientMockFactory::GET_ERROR_CODE);
        $this->expectExceptionMessage(FailingDatamapsClientMockFactory::GET_ERROR_MESSAGE);

        $client = FailingDatamapsClientMockFactory::make();
        $client->get("id");
    }

    public function testSearchRequest(): void
    {
        $this->expectException(DatamapsRequestFailedException::class);
        $this->expectExceptionCode(FailingDatamapsClientMockFactory::SEARCH_ERROR_CODE);
        $this->expectExceptionMessage(FailingDatamapsClientMockFactory::SEARCH_ERROR_MESSAGE);

        $client = FailingDatamapsClientMockFactory::make();
        $client->search(2);
    }

    public function testCreateRequest(): void
    {
        $this->expectException(DatamapsRequestFailedException::class);
        $this->expectExceptionCode(FailingDatamapsClientMockFactory::CREATE_ERROR_CODE);
        $this->expectExceptionMessage(FailingDatamapsClientMockFactory::CREATE_ERROR_MESSAGE);

        $client = FailingDatamapsClientMockFactory::make();
        $client->create(new Map("", [], "", []));
    }
}
