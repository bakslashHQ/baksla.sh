<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Infrastructure\StaticSiteGeneration;

use App\Shared\Infrastructure\StaticSiteGeneration\StaticPagesGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class StaticPagesGeneratorTest extends TestCase
{
    public function testGenerateContent(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $kernel->method('handle')->willReturn(new Response('foo-content'));

        $generator = new StaticPagesGenerator($kernel);
        ['content' => $content, 'format' => $format] = $generator->generate('/whatever');

        $this->assertSame('foo-content', $content);
        $this->assertNull($format);
    }

    public function testThrowOnNotOk(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $kernel->method('handle')->willReturn(new Response('not-found', \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND));

        $generator = new StaticPagesGenerator($kernel);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Expected URI "/whatever" to return status code 200, got 404.');
        $generator->generate('/whatever');
    }

    public function testThrowOnException(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $kernel->method('handle')->willThrowException(new \RuntimeException('Kernel error'));

        $generator = new StaticPagesGenerator($kernel);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot generate page for URI "/whatever".');
        $generator->generate('/whatever');
    }
}
