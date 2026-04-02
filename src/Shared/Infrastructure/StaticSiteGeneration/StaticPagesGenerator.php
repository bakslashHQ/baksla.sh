<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\StaticSiteGeneration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final readonly class StaticPagesGenerator
{
    public function __construct(
        private HttpKernelInterface $kernel,
    ) {
    }

    /**
     * @return array{content: string, format: ?string}
     *
     * @throws \RuntimeException
     */
    public function generate(string $uri): array
    {
        $request = Request::create($uri);

        try {
            $response = $this->kernel->handle($request, HttpKernelInterface::MAIN_REQUEST);
        } catch (\Exception $exception) {
            throw new \RuntimeException(\sprintf('Cannot generate page for URI "%s".', $uri), $exception->getCode(), $exception);
        }

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new \RuntimeException(\sprintf('Expected URI "%s" to return status code 200, got %d.', $uri, $response->getStatusCode()));
        }

        return [
            'content' => (string) $response->getContent(),
            'format' => $request->getFormat($response->headers->get('Content-Type')),
        ];
    }
}
