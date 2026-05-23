<?php

namespace App\DTOs;

use Carbon\Carbon;

class ErrorLogDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $message,
        public readonly string $exception,
        public readonly string $file,
        public readonly int $line,
        public readonly ?string $trace,
        public readonly Carbon $occurred_at,
        public readonly ?array $context = null,
        public readonly ?string $category = null,
        public readonly ?string $severity = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? uniqid(),
            message: $data['message'] ?? '',
            exception: $data['exception'] ?? 'Unknown',
            file: $data['file'] ?? '',
            line: $data['line'] ?? 0,
            trace: $data['trace'] ?? null,
            occurred_at: isset($data['occurred_at']) 
                ? Carbon::parse($data['occurred_at']) 
                : Carbon::now(),
            context: $data['context'] ?? null,
            category: $data['category'] ?? null,
            severity: $data['severity'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'exception' => $this->exception,
            'file' => $this->file,
            'line' => $this->line,
            'trace' => $this->trace,
            'occurred_at' => $this->occurred_at->toIso8601String(),
            'context' => $this->context,
            'category' => $this->category,
            'severity' => $this->severity,
        ];
    }

    public function getShortMessage(int $length = 100): string
    {
        return strlen($this->message) > $length 
            ? substr($this->message, 0, $length) . '...' 
            : $this->message;
    }

    public function getShortFile(): string
    {
        $parts = explode('/', $this->file);
        return count($parts) > 3 
            ? '.../' . implode('/', array_slice($parts, -3)) 
            : $this->file;
    }
}
