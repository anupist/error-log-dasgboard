<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Services\Api\ProjectErrorApiService;
use App\Services\ErrorAnalyzer\ErrorCategorizer;
use Livewire\Component;
use Livewire\Attributes\On;

class ProjectStats extends Component
{
    public Project $project;

    public int   $totalErrors    = 0;
    public int   $criticalErrors = 0;
    public int   $phpErrors      = 0;
    public int   $dbErrors       = 0;
    public int   $validationErrors = 0;
    public int   $apiErrors      = 0;
    public int   $queueErrors    = 0;
    public float $errorRate      = 0.0;
    public array $byCategory     = [];

    protected ProjectErrorApiService $apiService;
    protected ErrorCategorizer $categorizer;

    public function boot(ProjectErrorApiService $apiService, ErrorCategorizer $categorizer): void
    {
        $this->apiService  = $apiService;
        $this->categorizer = $categorizer;
    }

    public function mount(Project $project): void
    {
        $this->project = $project;
        $this->loadStats();
    }

    #[On('refresh-project')]
    public function loadStats(?int $projectId = null): void
    {
        if ($projectId !== null && $projectId !== $this->project->id) {
            return;
        }

        $errors      = $this->apiService->getTodayErrors($this->project);
        $categorized = collect($this->categorizer->categorizeCollection($errors));

        $this->totalErrors      = $categorized->count();
        $this->criticalErrors   = $categorized->where('severity', 'critical')->count();
        $this->byCategory       = $categorized->groupBy('category')->map->count()->toArray();

        $this->phpErrors        = $this->byCategory['php']        ?? 0;
        $this->dbErrors         = $this->byCategory['database']   ?? 0;
        $this->validationErrors = $this->byCategory['validation'] ?? 0;
        $this->apiErrors        = $this->byCategory['api']        ?? 0;
        $this->queueErrors      = $this->byCategory['queue']      ?? 0;

        $hoursToday      = max(1, now()->hour + 1);
        $this->errorRate = round($this->totalErrors / $hoursToday, 1);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.projects.project-stats');
    }
}
