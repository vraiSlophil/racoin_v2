<?php

declare(strict_types=1);

namespace Tests;

use mysqli;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\Support\HttpResponse;

abstract class IntegrationTestCase extends TestCase
{
    private static bool $applicationReady = false;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::waitForDatabase();
        self::waitForApplication();
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::resetDatabase();
    }

    protected function request(string $method, string $path, array $form = []): HttpResponse
    {
        $options = [
            'http' => [
                'method' => strtoupper($method),
                'ignore_errors' => true,
                'header' => "Connection: close\r\n",
            ],
        ];

        if ($form !== []) {
            $options['http']['header'] .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $options['http']['content'] = http_build_query($form);
        }

        $context = stream_context_create($options);
        $body = @file_get_contents(self::baseUrl() . $path, false, $context);

        $headers = $http_response_header ?? [];

        if ($body === false && $headers === []) {
            throw new RuntimeException(sprintf('HTTP request failed for %s %s', strtoupper($method), $path));
        }

        return new HttpResponse(
            self::extractStatusCode($headers),
            $body === false ? '' : $body,
            $headers,
        );
    }

    protected function fetchScalar(string $sql, string $types = '', mixed ...$params): mixed
    {
        $row = $this->fetchOne($sql, $types, ...$params);

        if ($row === null) {
            return null;
        }

        return array_values($row)[0];
    }

    protected function fetchOne(string $sql, string $types = '', mixed ...$params): ?array
    {
        $connection = self::databaseConnection();
        $statement = $connection->prepare($sql);

        if ($types !== '') {
            $statement->bind_param($types, ...$params);
        }

        $statement->execute();
        $result = $statement->get_result();
        $row = $result->fetch_assoc() ?: null;

        $result->free();
        $statement->close();
        $connection->close();

        return $row;
    }

    private static function waitForDatabase(): void
    {
        $lastException = null;

        for ($attempt = 0; $attempt < 30; $attempt++) {
            try {
                $connection = self::databaseConnection();
                $connection->close();
                return;
            } catch (\Throwable $exception) {
                $lastException = $exception;
                usleep(500000);
            }
        }

        throw new RuntimeException('Database is not reachable', previous: $lastException);
    }

    private static function waitForApplication(): void
    {
        if (self::$applicationReady) {
            return;
        }

        $lastException = null;

        for ($attempt = 0; $attempt < 30; $attempt++) {
            try {
                $context = stream_context_create([
                    'http' => [
                        'ignore_errors' => true,
                    ],
                ]);

                $body = @file_get_contents(self::baseUrl() . '/', false, $context);

                if ($body !== false) {
                    self::$applicationReady = true;
                    return;
                }
            } catch (\Throwable $exception) {
                $lastException = $exception;
            }

            usleep(500000);
        }

        throw new RuntimeException('Application HTTP endpoint is not reachable', previous: $lastException);
    }

    private static function resetDatabase(): void
    {
        $connection = self::databaseConnection();

        self::executeSqlScript($connection, self::projectRoot() . '/sql/create_schema.sql');
        self::executeSqlScript($connection, self::projectRoot() . '/sql/import_data.sql');

        $connection->close();
    }

    private static function executeSqlScript(mysqli $connection, string $path): void
    {
        $script = file_get_contents($path);

        if ($script === false) {
            throw new RuntimeException(sprintf('Unable to read SQL script: %s', $path));
        }

        $connection->multi_query($script);

        do {
            $result = $connection->store_result();
            if ($result !== false) {
                $result->free();
            }
        } while ($connection->more_results() && $connection->next_result());
    }

    private static function databaseConnection(): mysqli
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $config = parse_ini_file(self::projectRoot() . '/config/config.ini');

        if ($config === false) {
            throw new RuntimeException('Unable to load config/config.ini');
        }

        $connection = new mysqli(
            $config['host'],
            $config['username'],
            $config['password'],
            $config['database'],
        );

        $connection->set_charset($config['charset'] ?? 'utf8');

        return $connection;
    }

    private static function extractStatusCode(array $headers): int
    {
        if ($headers === []) {
            return 0;
        }

        if (preg_match('/\s(\d{3})\s/', $headers[0], $matches) !== 1) {
            return 0;
        }

        return (int) $matches[1];
    }

    private static function projectRoot(): string
    {
        return dirname(__DIR__);
    }

    private static function baseUrl(): string
    {
        return rtrim($_ENV['APP_BASE_URL'] ?? 'http://127.0.0.1:80', '/');
    }
}
