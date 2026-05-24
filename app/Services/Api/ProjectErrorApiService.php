<?php

namespace App\Services\Api;

use App\DTOs\ErrorLogDTO;
use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProjectErrorApiService
{
    private const ENDPOINT = '/log-errors/laravel';

    // ─── Public API ──────────────────────────────────────────────────────────

    /**
     * Fetch today's errors for a given project.
     */
    public function getTodayErrors(Project $project): Collection
    {
        return $this->getErrorsByDate($project, now()->toDateString());
    }

    /**
     * Fetch errors for a specific date.
     */
    public function getErrorsByDate(Project $project, string $date): Collection
    {
        if (! $this->isConfigured($project)) {
            return collect();
        }

        $cacheKey = $this->cacheKey($project, "errors_{$date}");

        return Cache::remember($cacheKey, $this->cacheTtl(), function () use ($project, $date) {
            return $this->fetchErrors($project, $date);
        });
    }

    /**
     * Get aggregated summary statistics for a project.
     */
    public function getSummary(Project $project): array
    {
        $cacheKey = $this->cacheKey($project, 'summary_' . now()->format('Y-m-d-H'));

        return Cache::remember($cacheKey, $this->cacheTtl(), function () use ($project) {
            $errors = $this->getTodayErrors($project);

            return [
                'total'       => $errors->count(),
                'critical'    => $errors->where('severity', 'critical')->count(),
                'by_category' => $errors->groupBy('category')->map->count()->toArray(),
                'by_hour'     => $errors->groupBy(fn ($e) => $e->occurred_at->format('H:00'))->map->count()->toArray(),
                'recent'      => $errors->sortByDesc('occurred_at')->take(10)->values()->toArray(),
            ];
        });
    }

    /**
     * Paginate errors with optional filters.
     */
    public function getPaginated(
        Project $project,
        int $page = 1,
        int $perPage = 20,
        ?string $category = null,
        ?string $severity = null,
        ?string $search = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
    ): array {
        $errors = $this->getTodayErrors($project);

        if ($category) {
            $errors = $errors->where('category', $category);
        }

        if ($severity) {
            $errors = $errors->where('severity', $severity);
        }

        if ($search) {
            $lower = strtolower($search);
            $errors = $errors->filter(
                fn ($e) => str_contains(strtolower($e->message), $lower)
                        || str_contains(strtolower($e->exception), $lower)
            );
        }

        $total    = $errors->count();
        $lastPage = max(1, (int) ceil($total / $perPage));

        return [
            'data'         => $errors->sortByDesc('occurred_at')->forPage($page, $perPage)->values(),
            'current_page' => $page,
            'per_page'     => $perPage,
            'total'        => $total,
            'last_page'    => $lastPage,
        ];
    }

    /**
     * Perform a health check against the project's API.
     * Returns status, response_time_ms, and last_error.
     */
    public function healthCheck(Project $project): array
    {
        if (! $this->isConfigured($project)) {
            return [
                'online'           => false,
                'response_time_ms' => null,
                'last_error'       => 'API URL not configured',
                'checked_at'       => now()->toIso8601String(),
            ];
        }

        $start = microtime(true);

        try {
            $request = Http::timeout(10);

            if ($project->api_token) {
                $request = $request->withToken($project->api_token);
            }

            $response = $request->get(rtrim($project->api_url, '/') . self::ENDPOINT, [
                'date' => now()->toDateString(),
            ]);

            $ms = (int) round((microtime(true) - $start) * 1000);

            return [
                'online'           => $response->successful(),
                'response_time_ms' => $ms,
                'last_error'       => $response->successful() ? null : "HTTP {$response->status()}",
                'checked_at'       => now()->toIso8601String(),
            ];
        } catch (\Exception $e) {
            $ms = (int) round((microtime(true) - $start) * 1000);

            return [
                'online'           => false,
                'response_time_ms' => $ms,
                'last_error'       => $e->getMessage(),
                'checked_at'       => now()->toIso8601String(),
            ];
        }
    }

    /**
     * Flush all cached data for a project.
     */
    public function clearCache(Project $project): void
    {
        // Laravel file/array cache doesn't support tag-based flush without Redis,
        // so we flush the whole cache. For Redis, use Cache::tags([...]).
        Cache::flush();
    }

    // ─── Private helpers ─────────────────────────────────────────────────────

    private function fetchErrors(Project $project, string $date): Collection
    {
        try {
            $request = Http::timeout(config('error-api.timeout', 15))
                ->retry(config('error-api.retry', 3), 100);

            if ($project->api_token) {
                $request = $request->withToken($project->api_token);
            }

            $response = $request->get(
                rtrim($project->api_url, '/') . self::ENDPOINT,
                ['date' => $date]
            );

            if (! $response->successful()) {
                Log::warning('ProjectErrorApiService: non-2xx response', [
                    'project' => $project->slug,
                    'status'  => $response->status(),
                ]);
                return collect();
            }

            $body   = $response->json();
            $errors = $body['data'] ?? $body['errors'] ?? $body;

            if (is_string($errors)) {
                $errors = json_decode($errors, true);
            }

            if (! is_array($errors)) {
                return collect();
            }

            return collect($errors)
                ->map(fn ($item) => is_array($item) ? ErrorLogDTO::fromArray($item) : null)
                ->filter();

        } catch (\Exception $e) {
            Log::error('ProjectErrorApiService: exception', [
                'project' => $project->slug,
                'message' => $e->getMessage(),
            ]);
            return collect();
        }
    }

    private function isConfigured(Project $project): bool
    {
        return ! empty($project->api_url)
            && ! str_contains($project->api_url, 'example.com');
    }

    private function cacheKey(Project $project, string $suffix): string
    {
        return "project_{$project->id}_{$suffix}";
    }

    private function cacheTtl(): int
    {
        return (int) config('error-api.cache_seconds', 60);
    }
}
