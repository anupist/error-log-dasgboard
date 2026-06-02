<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Services\ErrorAnalyzer\ErrorCategorizer;
use Livewire\Component;

class ProjectFilters extends Component
{
    public Project $project;

    public string $selectedCategory = '';
    public string $selectedSeverity = '';

    public array $categories = [];
    public array $severities = ['critical', 'error', 'warning', 'info'];

    protected ErrorCategorizer $categorizer;

    public function boot(ErrorCategorizer $categorizer): void
    {
        $this->categorizer = $categorizer;
    }

    public function mount(Project $project): void
    {
        $this->project    = $project;
        $this->categories = $this->categorizer->getCategories();
    }

    public function selectCategory(string $category): void
    {
        $this->selectedCategory = $this->selectedCategory === $category ? '' : $category;
        $this->dispatchFilters();
    }

    public function selectSeverity(string $severity): void
    {
        $this->selectedSeverity = $this->selectedSeverity === $severity ? '' : $severity;
        $this->dispatchFilters();
    }

    public function clearAll(): void
    {
        $this->selectedCategory = '';
        $this->selectedSeverity = '';
        $this->dispatchFilters();
    }

    private function dispatchFilters(): void
    {
        $this->dispatch('project-filters-changed',
            category: $this->selectedCategory,
            severity: $this->selectedSeverity,
        );
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.projects.project-filters');
    }
}
