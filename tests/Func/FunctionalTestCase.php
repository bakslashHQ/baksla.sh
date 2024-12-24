<?php

declare(strict_types=1);

namespace App\Tests\Func;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

abstract class FunctionalTestCase extends WebTestCase
{
    private static KernelBrowser|null $client;

    protected function setUp(): void
    {
        parent::setUp();
        self::ensureKernelShutdown();
        static::$client = static::createClient();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        static::$client = null;
        unset($this->legalMonologLogger);
    }

    protected static function createClient(array $options = [], array $server = []): KernelBrowser
    {
        return parent::createClient($options, $server + [
            'HTTPS' => true,
        ]);
    }

    protected function get(
        string $uri,
        array $parameters = [],
        array $files = [],
        array $server = [],
        ?string $content = null,
        bool $changeHistory = true
    ): Crawler {
        return self::$client->request(
            method: 'GET',
            uri: $uri,
            parameters: $parameters,
            files: $files,
            server: $server,
            content: $content,
            changeHistory: $changeHistory
        );
    }

    protected function followRedirect(): Crawler
    {
        return self::$client->followRedirect();
    }

    /**
     * @template T
     * @param class-string<T> $class
     * @return object<T>
     */
    protected function getService(string $class): object
    {
        $service = self::getContainer()->get($class);

        self::assertInstanceOf($class, $service);

        return $service;
    }
}
