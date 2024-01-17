<?php

namespace DatamapsPHP\Tests;

use DatamapsPHP\DatamapsClientFactory;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DatamapsClientFactoryTest extends TestCase
{
    public function testBasicCreation(): void
    {
        $client = DatamapsClientFactory::make();

        $datamapsClientReflection = new ReflectionClass($client::class);
        $httpClientValue = $datamapsClientReflection->getProperty("httpClient")->getValue($client);

        $this->assertInstanceOf(HttpClientInterface::class, $httpClientValue);
        $this->assertNotInstanceOf(MockHttpClient::class, $httpClientValue);
    }
}
