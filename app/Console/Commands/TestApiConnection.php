<?php

namespace App\Console\Commands;

use App\Services\Api\ErrorApiService;
use App\Services\ErrorAnalyzer\ErrorCategorizer;
use Illuminate\Console\Command;

class TestApiConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the Error API connection and display results';

    /**
     * Execute the console command.
     */
    public function handle(ErrorApiService $apiService, ErrorCategorizer $categorizer)
    {
        $this->info('Testing Error API Connection...');
        $this->newLine();

        // Test API configuration
        $this->info('API Configuration:');
        $this->line('Base URL: ' . config('error-api.base_url'));
        $this->line('Endpoint: ' . config('error-api.endpoints.errors'));
        $this->line('Full URL: ' . config('error-api.base_url') . config('error-api.endpoints.errors'));
        $this->newLine();

        // Fetch errors
        $this->info('Fetching today\'s errors...');
        $errors = $apiService->getTodayErrors();

        if ($errors->isEmpty()) {
            $this->warn('No errors found or API connection failed.');
            $this->info('Check your API endpoint configuration in .env file.');
            return 0;
        }

        $this->info('Successfully fetched ' . $errors->count() . ' errors!');
        $this->newLine();

        // Categorize errors
        $categorized = collect($categorizer->categorizeCollection($errors));

        // Display statistics
        $this->info('Error Statistics:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Errors', $errors->count()],
                ['Critical', $categorized->where('severity', 'critical')->count()],
                ['Errors', $categorized->where('severity', 'error')->count()],
                ['Warnings', $categorized->where('severity', 'warning')->count()],
            ]
        );
        $this->newLine();

        // Display categories
        $categories = $categorized->groupBy('category')->map->count();
        $this->info('Errors by Category:');
        $this->table(
            ['Category', 'Count'],
            $categories->map(fn($count, $category) => [$category, $count])->values()->toArray()
        );
        $this->newLine();

        // Display recent errors
        $this->info('Recent Errors (Last 5):');
        $recent = $categorized->sortByDesc('occurred_at')->take(5);
        
        $this->table(
            ['Time', 'Exception', 'Message', 'Category', 'Severity'],
            $recent->map(function ($error) {
                return [
                    $error->occurred_at->format('H:i:s'),
                    $error->exception,
                    substr($error->message, 0, 50) . '...',
                    $error->category ?? 'general',
                    $error->severity ?? 'info',
                ];
            })->toArray()
        );

        $this->newLine();
        $this->info('✓ API connection test completed successfully!');
        
        return 0;
    }
}
