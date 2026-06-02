<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Services\Api\ProjectErrorApiService;
use App\Services\ErrorAnalyzer\ErrorCategorizer;
use Livewire\Component;
use Livewire\Attributes\On;

class ProjectCharts extends Component
{
    public Project $project;

    /** 'trend' or 'category' */
    public string $chartType = 'trend';

    public array $chartData    = ['labels' => [], 'data' => []];
    public array $categoryData = ['labels' => [], 'data' => []];

    protected ProjectErrorApiService $apiService;
    protected ErrorCategorizer $categorizer;

    public function boot(ProjectErrorApiService $apiService, ErrorCategorizer $categorizer): void
    {
        $this->apiService  = $apiService;
        $this->categorizer = $categorizer;
    }

    public function mount(Project $project, string $chartType = 'trend'): void
    {
        $this->project   = $project;
        $this->chartType = $chartType;
        // Charts start empty; ProjectDashboard will fire log-file-changed after mount
    }

    #[On('log-file-changed')]
    public function loadChartData(int $projectId, string $logFile): void
    {
        if ($projectId !== $this->project->id) {
            return;
        }

        $errors      = $this->apiService->getErrorsByLogFile($this->project, $logFile);
        $categorized = collect($this->categorizer->categorizeCollection($errors));

        // Build hourly buckets across all 24 hours
        $hourly = [];
        for ($h = 0; $h < 24; $h++) {
            $hourly[sprintf('%02d:00', $h)] = 0;
        }

        foreach ($categorized->groupBy(fn ($e) => $e->occurred_at->format('H:00')) as $hour => $group) {
            if (array_key_exists($hour, $hourly)) {
                $hourly[$hour] = $group->count();
            }
        }

        $this->chartData = [
            'labels' => array_keys($hourly),
            'data'   => array_values($hourly),
        ];

        $groups = $categorized->groupBy('category');
        $this->categoryData = [
            'labels' => $groups->keys()->toArray(),
            'data'   => $groups->map->count()->values()->toArray(),
        ];
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.projects.project-charts');
    }
}
