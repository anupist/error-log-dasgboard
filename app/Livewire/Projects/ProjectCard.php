<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Services\Api\ProjectErrorApiService;
use App\Services\ErrorAnalyzer\ErrorCategorizer;
use Livewire\Component;

class ProjectCard extends Component
{
    public Project $project;

    public int    $totalErrors    = 0;
    public int    $criticalErrors = 0;
    public bool   $apiOnline      = false;
    public ?int   $responseTimeMs = null;
    public bool   $loading        = true;
    public string $lastChecked    = '';

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
    }

    /**
     * Called lazily after the component mounts in the browser.
     */
    public function loadStats(): void
    {
        $errors      = $this->apiService->getTodayErrors($this->project);
        $categorized = collect($this->categorizer->categorizeCollection($errors));

        $this->totalErrors    = $categorized->count();
        $this->criticalErrors = $categorized->where('severity', 'critical')->count();

        $health = $this->apiService->healthCheck($this->project);

        $this->apiOnline      = $health['online'];
        $this->responseTimeMs = $health['response_time_ms'];
        $this->lastChecked    = now()->format('H:i:s');
        $this->loading        = false;
    }

    /** Bubble edit intent up to the parent ProjectList component */
    public function triggerEdit(): void
    {
        $this->dispatch('open-edit-modal', id: $this->project->id);
    }

    /** Bubble delete intent up to the parent ProjectList component */
    public function triggerDelete(): void
    {
        $this->dispatch('confirm-delete', id: $this->project->id);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.projects.project-card');
    }
}
