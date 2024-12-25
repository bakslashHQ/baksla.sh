<?php

declare(strict_types=1);

namespace App\Tests\Func;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class FunctionalTestCase extends WebTestCase
{
    private static KernelBrowser|null $client;

    protected function setUp(): void
    {
        parent::setUp();
        self::ensureKernelShutdown();
        self::$client = self::createClient();
    }

    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        self::$client = null;
    }

    /**
     * @param array<string, mixed> $options An array of options to pass to the createKernel method
     * @param array<string, mixed> $server  An array of server parameters
     */
    #[\Override]
    protected static function createClient(array $options = [], array $server = []): KernelBrowser
    {
        return parent::createClient($options, $server + [
            'HTTPS' => true,
        ]);
    }

    /**
     * @param array<string, mixed>  $parameters    The Request parameters
     * @param array<UploadedFile>  $files         The files
     * @param array<string, mixed>  $server        The server parameters (HTTP headers are referenced with an HTTP_ prefix as PHP does)
     */
    protected function get(
        string $uri,
        array $parameters = [],
        array $files = [],
        array $server = [],
        ?string $content = null,
        bool $changeHistory = true
    ): Crawler {
        $this->assertNotNull(self::$client, 'The client must be set.');

        return self::$client->request(
            method: \Symfony\Component\HttpFoundation\Request::METHOD_GET,
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
        $this->assertNotNull(self::$client, 'The client must be set.');

        return self::$client->followRedirect();
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
    protected function getService(string $class): object
    {
        $service = self::getContainer()->get($class);

        $this->assertInstanceOf($class, $service);

        return $service;
    }
}
