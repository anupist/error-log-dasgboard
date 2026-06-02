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

    public int   $totalErrors       = 0;
    public int   $criticalErrors    = 0;
    public int   $phpErrors         = 0;
    public int   $dbErrors          = 0;
    public int   $validationErrors  = 0;
    public int   $apiErrors         = 0;
    public int   $queueErrors       = 0;
    public int   $cacheErrors       = 0;
    public int   $otherErrors       = 0;
    public float $errorRate         = 0.0;
    public array $byCategory        = [];
    public string $currentLogFile   = '';

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
        // Stats start empty; ProjectDashboard will fire log-file-changed after mount
    }

    #[On('log-file-changed')]
    public function loadStats(int $projectId, string $logFile): void
    {
        if ($projectId !== $this->project->id) {
            return;
        }

        $this->currentLogFile = $logFile;

        $errors      = $this->apiService->getErrorsByLogFile($this->project, $logFile);
        $categorized = collect($this->categorizer->categorizeCollection($errors));

        $this->totalErrors      = $categorized->count();
        $this->criticalErrors   = $categorized->where('severity', 'critical')->count();
        $this->byCategory       = $categorized->groupBy('category')->map->count()->toArray();

        $this->phpErrors        = $this->byCategory['php']        ?? 0;
        $this->dbErrors         = $this->byCategory['database']   ?? 0;
        $this->validationErrors = $this->byCategory['validation'] ?? 0;
        $this->apiErrors        = $this->byCategory['api']        ?? 0;
        $this->queueErrors      = $this->byCategory['queue']      ?? 0;
        $this->cacheErrors      = $this->byCategory['cache']      ?? 0;

        $knownCategories     = ['php', 'database', 'validation', 'api', 'queue', 'cache', 'authentication'];
        $this->otherErrors   = $categorized->whereNotIn('category', $knownCategories)->count();

        // Rate/hr based on the log file's actual timespan
        $hoursInFile     = max(1, $errors->count() > 0
            ? max(1, (int) $errors->sortBy('occurred_at')->first()->occurred_at->diffInHours(
                $errors->sortByDesc('occurred_at')->first()->occurred_at
            ) + 1)
            : 1
        );
        $this->errorRate = round($this->totalErrors / $hoursInFile, 1);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.projects.project-stats');
    }
}
