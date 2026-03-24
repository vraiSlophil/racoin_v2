<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Generator;
use RuntimeException;

final class OpenApiDocumentFactory
{
    public function __construct(
        private readonly string $sourceDirectory = __DIR__
    ) {}

    public function generateJson(string $serverUrl = '/'): string
    {
        $openApi = (new Generator())->generate([$this->sourceDirectory]);

        if ($openApi === null) {
            throw new RuntimeException('Unable to generate the OpenAPI document.');
        }

        /** @var array<string, mixed> $document */
        $document = json_decode($openApi->toJson(), true, 512, JSON_THROW_ON_ERROR);
        $document['servers'] = [
            [
                'url' => $this->normalizeServerUrl($serverUrl),
            ],
        ];

        return json_encode(
            $document,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
    }

    private function normalizeServerUrl(string $serverUrl): string
    {
        $serverUrl = trim($serverUrl);

        if ($serverUrl === '' || $serverUrl === '/') {
            return '/';
        }

        if (str_starts_with($serverUrl, 'http://') || str_starts_with($serverUrl, 'https://')) {
            return rtrim($serverUrl, '/');
        }

        return '/' . trim($serverUrl, '/');
    }
}
