<?php

namespace App\Services\Api;

use App\DTOs\ErrorLogDTO;
use App\Models\Project;
use App\Services\ErrorAnalyzer\LaravelLogParser;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProjectErrorApiService
{
    /** Endpoint for listing all log files */
    private const ENDPOINT_LIST = '/log-errors';

    /** Endpoint for fetching a specific log file: /log-errors/{name} */
    private const ENDPOINT_FILE = '/log-errors/';

    // ─── Public API ──────────────────────────────────────────────────────────

    /**
     * Fetch the list of available log files for a project.
     *
     * API response shape:
     * {
     *   "success": true,
     *   "data": {
     *     "total_files": 18,
     *     "files": [
     *       { "filename": "laravel-2026-04-29.log", "size": 166380,
     *         "size_human": "162.48 KB", "last_modified": "2026-04-29 19:11:52" }
     *     ]
     *   }
     * }
     *
     * @return array<int, array{filename: string, size: int, size_human: string, last_modified: string}>
     */
    public function getLogFiles(Project $project): array
    {
        if (! $this->isConfigured($project)) {
            return [];
        }

        $cacheKey = $this->cacheKey($project, 'log_files_' . now()->format('Y-m-d-H'));

        return Cache::remember($cacheKey, $this->cacheTtl(), function () use ($project) {
            try {
                $request = Http::timeout(config('error-api.timeout', 15));

                if ($project->api_token) {
                    $request = $request->withToken($project->api_token);
                }

                $url      = rtrim($project->api_url, '/') . self::ENDPOINT_LIST;
                $response = $request->get($url);

                if (! $response->successful()) {
                    Log::warning('ProjectErrorApiService: getLogFiles non-2xx', [
                        'project' => $project->slug,
                        'status'  => $response->status(),
                        'url'     => $url,
                    ]);
                    return [];
                }

                $body  = $response->json();
                $files = $body['data']['files'] ?? [];

                if (! is_array($files)) {
                    return [];
                }

                Log::info('Log Files Loaded', [
                    'project' => $project->slug,
                    'count'   => count($files),
                ]);

                // Sort newest first
                usort($files, fn ($a, $b) => strcmp($b['last_modified'] ?? '', $a['last_modified'] ?? ''));

                return $files;

            } catch (\Exception $e) {
                Log::error('ProjectErrorApiService: getLogFiles exception', [
                    'project' => $project->slug,
                    'message' => $e->getMessage(),
                ]);
                return [];
            }
        });
    }

    /**
     * Determine the newest log filename for a project.
     * Returns null when no files are available.
     */
    public function getNewestLogFile(Project $project): ?string
    {
        $files = $this->getLogFiles($project);

        if (empty($files)) {
            return null;
        }

        // getLogFiles() already sorts newest-first
        return $files[0]['filename'] ?? null;
    }

    /**
     * Fetch and parse errors from a specific log file.
     *
     * @param  string  $filename  e.g. "laravel-2026-05-19.log"
     */
    public function getErrorsByLogFile(Project $project, string $filename): Collection
    {
        if (! $this->isConfigured($project)) {
            return collect();
        }

        $cacheKey = $this->cacheKey($project, 'file_' . md5($filename));

        return Cache::remember($cacheKey, $this->cacheTtl(), function () use ($project, $filename) {
            return $this->fetchErrorsByFilename($project, $filename);
        });
    }

    /**
     * @deprecated  Use getErrorsByLogFile() instead.
     * Kept for backward-compat with anything that still calls getTodayErrors().
     */
    public function getTodayErrors(Project $project): Collection
    {
        $newest = $this->getNewestLogFile($project);

        if ($newest === null) {
            return collect();
        }

        return $this->getErrorsByLogFile($project, $newest);
    }

    /**
     * Get aggregated summary statistics for a project (based on newest log file).
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
     * Perform a health check against the project's API.
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

            $response = $request->get(rtrim($project->api_url, '/') . self::ENDPOINT_LIST);
            $ms       = (int) round((microtime(true) - $start) * 1000);

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
        Cache::flush();
    }

    // ─── Private helpers ─────────────────────────────────────────────────────

    /**
     * Call the API for a specific log filename and return parsed errors.
     */
    private function fetchErrorsByFilename(Project $project, string $filename): Collection
    {
        try {
            // Strip .log extension → "laravel-2026-05-19.log" becomes "laravel-2026-05-19"
            $name    = preg_replace('/\.log$/i', '', $filename);
            $url     = rtrim($project->api_url, '/') . self::ENDPOINT_FILE . $name;

            Log::info('Selected Log File', [
                'project'  => $project->slug,
                'file'     => $filename,
                'url'      => $url,
            ]);

            $request = Http::timeout(config('error-api.timeout', 15))
                ->retry(config('error-api.retry', 3), 100);

            if ($project->api_token) {
                $request = $request->withToken($project->api_token);
            }

            $response = $request->get($url);

            if (! $response->successful()) {
                Log::warning('ProjectErrorApiService: fetchErrorsByFilename non-2xx', [
                    'project'  => $project->slug,
                    'filename' => $filename,
                    'status'   => $response->status(),
                ]);
                return collect();
            }

            $body = $response->json();

            // Primary path: { "data": { "content": "raw log string" } }
            if (isset($body['data']['content']) && is_string($body['data']['content'])) {
                return LaravelLogParser::parse($body['data']['content']);
            }

            // Fallback: structured errors array
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
            Log::error('ProjectErrorApiService: fetchErrorsByFilename exception', [
                'project'  => $project->slug,
                'filename' => $filename,
                'message'  => $e->getMessage(),
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
