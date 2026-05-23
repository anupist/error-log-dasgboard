<?php

namespace App\Services\ErrorAnalyzer;

use App\DTOs\ErrorLogDTO;

class ErrorCategorizer
{
    private const PATTERNS = [
        'database' => [
            'SQLSTATE',
            'QueryException',
            'PDOException',
            'database',
            'SQL',
        ],
        'php' => [
            'Undefined variable',
            'Undefined array key',
            'TypeError',
            'ParseError',
            'Call to undefined',
        ],
        'validation' => [
            'ValidationException',
            'validation failed',
            'The given data was invalid',
        ],
        'authentication' => [
            'AuthenticationException',
            'Unauthenticated',
            'AuthorizationException',
            'Unauthorized',
        ],
        'api' => [
            'GuzzleHttp',
            'cURL error',
            'HTTP request',
            'API',
        ],
        'queue' => [
            'Queue',
            'Job',
            'timeout',
            'MaxAttemptsExceededException',
        ],
        'cache' => [
            'RedisException',
            'Cache',
            'Memcached',
        ],
    ];

    private const SEVERITY_PATTERNS = [
        'critical' => [
            'SQLSTATE',
            'Fatal error',
            'ParseError',
            'OutOfMemoryError',
            'database',
        ],
        'error' => [
            'Exception',
            'Error',
            'Failed',
        ],
        'warning' => [
            'Warning',
            'Deprecated',
        ],
    ];

    /**
     * Categorize an error based on its content
     */
    public function categorize(ErrorLogDTO $error): array
    {
        $category = $this->detectCategory($error);
        $severity = $this->detectSeverity($error);

        return [
            'category' => $category,
            'severity' => $severity,
        ];
    }

    /**
     * Detect error category
     */
    private function detectCategory(ErrorLogDTO $error): string
    {
        $searchText = strtolower($error->message . ' ' . $error->exception);

        foreach (self::PATTERNS as $category => $patterns) {
            foreach ($patterns as $pattern) {
                if (str_contains($searchText, strtolower($pattern))) {
                    return $category;
                }
            }
        }

        return 'general';
    }

    /**
     * Detect error severity
     */
    private function detectSeverity(ErrorLogDTO $error): string
    {
        $searchText = strtolower($error->message . ' ' . $error->exception);

        foreach (self::SEVERITY_PATTERNS as $severity => $patterns) {
            foreach ($patterns as $pattern) {
                if (str_contains($searchText, strtolower($pattern))) {
                    return $severity;
                }
            }
        }

        return 'info';
    }

    /**
     * Categorize a collection of errors
     */
    public function categorizeCollection($errors): array
    {
        return $errors->map(function ($error) {
            $categorization = $this->categorize($error);
            
            return new ErrorLogDTO(
                id: $error->id,
                message: $error->message,
                exception: $error->exception,
                file: $error->file,
                line: $error->line,
                trace: $error->trace,
                occurred_at: $error->occurred_at,
                context: $error->context,
                category: $categorization['category'],
                severity: $categorization['severity'],
            );
        })->all();
    }

    /**
     * Get available categories
     */
    public function getCategories(): array
    {
        return array_keys(self::PATTERNS);
    }

    /**
     * Get available severities
     */
    public function getSeverities(): array
    {
        return array_keys(self::SEVERITY_PATTERNS);
    }
}
