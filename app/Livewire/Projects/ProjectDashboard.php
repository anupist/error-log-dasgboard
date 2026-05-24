<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;

class ProjectDashboard extends Component
{
    public Project $project;
    public int $refreshInterval;

    public function mount(Project $project): void
    {
        $this->project         = $project;
        $this->refreshInterval = (int) config('error-api.auto_refresh', 30);
    }

    public function refreshData(): void
    {
        $this->dispatch('refresh-project', projectId: $this->project->id);
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
