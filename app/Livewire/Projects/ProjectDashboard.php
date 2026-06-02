<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Services\Api\ProjectErrorApiService;
use Livewire\Component;

class ProjectDashboard extends Component
{
    public Project $project;
    public int     $refreshInterval;

    /** Currently selected log filename, e.g. "laravel-2026-05-19.log" */
    public string $selectedLogFile = '';

    /** List of available log files from the API */
    public array $logFiles = [];

    protected ProjectErrorApiService $apiService;

    public function boot(ProjectErrorApiService $apiService): void
    {
        $this->apiService = $apiService;
    }

    public function mount(Project $project): void
    {
        $this->project         = $project;
        $this->refreshInterval = (int) config('error-api.auto_refresh', 30);

        $this->loadLogFiles();
    }

    /**
     * Load available log files and auto-select the newest one.
     */
    public function loadLogFiles(): void
    {
        $this->logFiles = $this->apiService->getLogFiles($this->project);

        // Auto-select newest (already sorted desc by the service)
        if (! empty($this->logFiles)) {
            $this->selectedLogFile = $this->logFiles[0]['filename'] ?? '';
        }
    }

    /**
     * Called by the view when user picks a different log file.
     */
    public function selectLogFile(string $filename): void
    {
        $this->selectedLogFile = $filename;

        // Tell all child components to reload with the new file
        $this->dispatch('log-file-changed',
            projectId: $this->project->id,
            logFile:   $filename,
        );
    }

    public function refreshData(): void
    {
        // Bust the log file list cache so fresh files appear
        $this->apiService->clearCache($this->project);
        $this->loadLogFiles();

        $this->dispatch('log-file-changed',
            projectId: $this->project->id,
            logFile:   $this->selectedLogFile,
        );
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.projects.project-dashboard')
            ->layout('layouts.app', [
                'header'    => $this->project->name,
                'subheader' => ucfirst($this->project->environment) . ' · ' . $this->project->slug,
            ]);
    }
}
