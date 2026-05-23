<?php

namespace App\Livewire\Dashboard;

use App\Services\Api\ErrorApiService;
use App\Services\ErrorAnalyzer\ErrorCategorizer;
use Livewire\Component;
use Livewire\Attributes\On;

class ErrorTrendChart extends Component
{
    public $chartData = [
        'labels' => [],
        'data' => []
    ];
    public $categoryData = [
        'labels' => [],
        'data' => []
    ];

    protected $errorApiService;
    protected $errorCategorizer;

    public function boot(ErrorApiService $errorApiService, ErrorCategorizer $errorCategorizer)
    {
        $this->errorApiService = $errorApiService;
        $this->errorCategorizer = $errorCategorizer;
    }

    public function mount()
    {
        $this->loadChartData();
    }

    #[On('refresh-dashboard')]
    public function loadChartData()
    {
        $errors = $this->errorApiService->getTodayErrors();
        $categorizedErrors = collect($this->errorCategorizer->categorizeCollection($errors));

        // Prepare hourly trend data
        $hourlyData = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $hourlyData[sprintf('%02d:00', $hour)] = 0;
        }

        $errorsByHour = $categorizedErrors->groupBy(function ($error) {
            return $error->occurred_at->format('H:00');
        });

        foreach ($errorsByHour as $hour => $errors) {
            $hourlyData[$hour] = $errors->count();
        }

        $this->chartData = [
            'labels' => array_keys($hourlyData),
            'data' => array_values($hourlyData),
        ];

        // Prepare category data
        $categoryGroups = $categorizedErrors->groupBy('category');
        $this->categoryData = [
            'labels' => $categoryGroups->keys()->toArray(),
            'data' => $categoryGroups->map->count()->values()->toArray(),
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.error-trend-chart');
    }
}
