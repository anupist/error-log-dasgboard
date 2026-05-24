<?php

namespace App\Providers;

use App\Services\Api\ErrorApiService;
use App\Services\Api\ProjectErrorApiService;
use App\Services\ErrorAnalyzer\ErrorCategorizer;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Legacy single-project service (kept for backward compatibility)
        $this->app->singleton(ErrorApiService::class);

        // Multi-project services
        $this->app->singleton(ProjectErrorApiService::class);
        $this->app->singleton(ErrorCategorizer::class);
    }

    public function boot(): void
    {
        //
    }
}
