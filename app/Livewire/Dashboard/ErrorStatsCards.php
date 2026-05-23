<?php

namespace App\Livewire\Dashboard;

use App\Services\Api\ErrorApiService;
use App\Services\ErrorAnalyzer\ErrorCategorizer;
use Livewire\Component;
use Livewire\Attributes\On;

class ErrorStatsCards extends Component
{
    public $totalErrors = 0;
    public $criticalErrors = 0;
    public $errorsByCategory = [];
    public $errorRate = 0;

    protected $errorApiService;
    protected $errorCategorizer;

    public function boot(ErrorApiService $errorApiService, ErrorCategorizer $errorCategorizer)
    {
        $this->errorApiService = $errorApiService;
        $this->errorCategorizer = $errorCategorizer;
    }

    public function mount()
    {
        $this->loadStats();
    }

    #[On('refresh-dashboard')]
    public function loadStats()
    {
        $errors = $this->errorApiService->getTodayErrors();
        
        // Categorize errors
        $categorizedErrors = collect($this->errorCategorizer->categorizeCollection($errors));

        $this->totalErrors = $categorizedErrors->count();
        $this->criticalErrors = $categorizedErrors->where('severity', 'critical')->count();
        $this->errorsByCategory = $categorizedErrors->groupBy('category')->map->count()->toArray();
        
        // Calculate error rate (errors per hour)
        $hoursToday = now()->hour + 1;
        $this->errorRate = $hoursToday > 0 ? round($this->totalErrors / $hoursToday, 1) : 0;
    }

    public function render()
    {
        return view('livewire.dashboard.error-stats-cards');
    }
}
