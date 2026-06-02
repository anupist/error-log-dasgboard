<?php

namespace App\Services\ErrorAnalyzer;

use App\DTOs\ErrorLogDTO;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class LaravelLogParser
{
    /**
     * Regex to match the opening line of a Laravel log entry.
     * Captures: timestamp, environment, level, and the first line of the message.
     *
     * Example:
     *   [2026-05-19 16:37:09] local.ERROR: file_put_contents(...) failed ...
     */
    private const ENTRY_PATTERN =
        '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+(\w+)\.(\w+):\s+(.*)/m';

    /**
     * Parse a raw Laravel log string and return a Collection of ErrorLogDTO.
     *
     * @param  string|null  $rawContent
     * @return Collection<ErrorLogDTO>
     */
    public static function parse(?string $rawContent): Collection
    {
        if (empty($rawContent)) {
            return collect();
        }

        // Split the log into individual entry blocks.
        // Each new entry starts with "[YYYY-MM-DD HH:MM:SS]".
        $blocks = preg_split(
            '/(?=^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\])/m',
            $rawContent,
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        if (empty($blocks)) {
            return collect();
        }

        $parsed = collect($blocks)
            ->map(fn (string $block) => self::parseBlock(trim($block)))
            ->filter(); // remove nulls (non-matching lines)

        Log::info('LaravelLogParser: Parsed Errors Count', [
            'count' => $parsed->count(),
        ]);

        return $parsed;
    }

    // ─── Private helpers ─────────────────────────────────────────────────────

    /**
     * Parse a single log block into an ErrorLogDTO, or return null if it
     * does not match a valid Laravel log entry.
     */
    private static function parseBlock(string $block): ?ErrorLogDTO
    {
        if (! preg_match(self::ENTRY_PATTERN, $block, $matches)) {
            return null;
        }

        [, $timestamp, $environment, $level, $firstLine] = $matches;

        // Only process ERROR and CRITICAL levels (skip DEBUG, INFO, WARNING, etc.)
        $upperLevel = strtoupper($level);
        if (! in_array($upperLevel, ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'], true)) {
            return null;
        }

        // Extract exception class from the first line if present.
        // Pattern: "ExceptionClass: message" or just the message itself.
        $exception = 'Unknown';
        $message   = $firstLine;

        if (preg_match('/^([A-Za-z\\\\]+Exception|[A-Za-z\\\\]+Error)\s*:\s*(.*)/s', $firstLine, $exMatches)) {
            $exception = $exMatches[1];
            $message   = $exMatches[2];
        }

        // Extract file + line from stack trace if present.
        // Pattern: " at /path/to/file.php:123"  or  "#0 /path/to/file.php(123)"
        $file = '';
        $line = 0;

        if (preg_match('/at\s+(\/[^\s:]+\.php):(\d+)/i', $block, $fileMatch)) {
            $file = $fileMatch[1];
            $line = (int) $fileMatch[2];
        } elseif (preg_match('/#0\s+([^\s(]+\.php)\((\d+)\)/', $block, $fileMatch)) {
            $file = $fileMatch[1];
            $line = (int) $fileMatch[2];
        }

        // Keep the full block as trace (minus the first line).
        $traceLines = array_slice(explode("\n", $block), 1);
        $trace      = implode("\n", $traceLines);
        $trace      = trim($trace) ?: null;

        return new ErrorLogDTO(
            id:          uniqid('log_', true),
            message:     trim($message),
            exception:   $exception,
            file:        $file,
            line:        $line,
            trace:       $trace,
            occurred_at: Carbon::parse($timestamp),
            context:     [
                'environment' => $environment,
                'level'       => $upperLevel,
                'full_log'    => $block,
            ],
        );
    }
}
