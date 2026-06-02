<?php

namespace App\Services\Api;

use App\DTOs\ErrorLogDTO;
use App\Services\ErrorAnalyzer\LaravelLogParser;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ErrorApiService
{
    private string $baseUrl;
    private int $timeout;
    private int $cacheSeconds;
    private int $retry;

    public function __construct()
    {
        $this->baseUrl = config('error-api.base_url');
        $this->timeout = config('error-api.timeout');
        $this->cacheSeconds = config('error-api.cache_seconds');
        $this->retry = config('error-api.retry');
    }

    /**
     * Get today's error logs
     */
    public function getTodayErrors(): Collection
    {
        return $this->getErrorsByDate(now()->toDateString());
    }

    /**
     * Get error logs by specific date
     */
    public function getErrorsByDate(string $date): Collection
    {
        // Check if API is configured
        if ($this->baseUrl === 'https://example.com') {
            // Return empty collection if using default/example URL
            return collect();
        }

        $cacheKey = "error_logs_{$date}";

        return Cache::remember($cacheKey, $this->cacheSeconds, function () use ($date) {
            try {
                $response = Http::timeout($this->timeout)
                    ->retry($this->retry, 100)
                    ->get($this->baseUrl . config('error-api.endpoints.errors'), [
                        'date' => $date,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();

                    // ── New path: API returns raw log content string ──────────
                    // Shape: { "success": true, "data": { "content": "...", "log_file": "..." } }
                    if (isset($data['data']['content']) && is_string($data['data']['content'])) {
                        return LaravelLogParser::parse($data['data']['content']);
                    }

                    // ── Legacy path: structured errors array ──────────────────
                    $errors = $data['data'] ?? $data['errors'] ?? $data;

                    // If errors is a string (JSON encoded), decode it
                    if (is_string($errors)) {
                        $errors = json_decode($errors, true);
                    }

                    if (!is_array($errors)) {
                        return collect();
                    }

                    return collect($errors)->map(function ($error) {
                        // Skip if error is not an array
                        if (!is_array($error)) {
                            return null;
                        }

                        return ErrorLogDTO::fromArray($error);
                    })->filter(); // Remove null values
                }

                Log::warning('Error API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return collect();
            } catch (\Exception $e) {
                Log::error('Error API exception', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                return collect();
            }
        });
    }

    /**
     * Get error summary statistics
     */
    public function getSummary(): array
    {
        $cacheKey = 'error_summary_' . now()->format('Y-m-d-H');

        return Cache::remember($cacheKey, $this->cacheSeconds, function () {
            $errors = $this->getTodayErrors();

            return [
                'total' => $errors->count(),
                'critical' => $errors->where('severity', 'critical')->count(),
                'by_category' => $errors->groupBy('category')->map->count()->toArray(),
                'by_hour' => $errors->groupBy(function ($error) {
                    return $error->occurred_at->format('H:00');
                })->map->count()->toArray(),
                'recent' => $errors->sortByDesc('occurred_at')->take(10)->values()->toArray(),
            ];
        });
    }

    /**
     * Get errors with pagination
     */
    public function getErrorsPaginated(int $page = 1, int $perPage = 20, ?string $category = null, ?string $search = null): array
    {
        $errors = $this->getTodayErrors();

        // Apply filters
        if ($category) {
            $errors = $errors->where('category', $category);
        }

        if ($search) {
            $errors = $errors->filter(function ($error) use ($search) {
                return str_contains(strtolower($error->message), strtolower($search)) ||
                       str_contains(strtolower($error->exception), strtolower($search));
            });
        }

        $total = $errors->count();
        $lastPage = ceil($total / $perPage);

        $items = $errors
            ->sortByDesc('occurred_at')
            ->forPage($page, $perPage)
            ->values();

        return [
            'data' => $items,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => $lastPage,
        ];
    }

    /**
     * Clear cache
     */
    public function clearCache(): void
    {
        Cache::flush();
    }
}
