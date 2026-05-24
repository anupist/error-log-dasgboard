<?php

namespace App\Livewire\Projects;

use App\DTOs\ErrorLogDTO;
use App\Models\Project;
use App\Services\Api\ProjectErrorApiService;
use App\Services\ErrorAnalyzer\ErrorCategorizer;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class ProjectErrorsTable extends Component
{
    use WithPagination;

    public Project $project;

    public string  $search           = '';
    public string  $categoryFilter   = '';
    public string  $severityFilter   = '';
    public string  $dateFrom         = '';
    public string  $dateTo           = '';
    public int     $perPage          = 20;
    public ?ErrorLogDTO $selectedError = null;

    protected ProjectErrorApiService $apiService;
    protected ErrorCategorizer $categorizer;

    public function boot(ProjectErrorApiService $apiService, ErrorCategorizer $categorizer): void
    {
        $this->apiService  = $apiService;
        $this->categorizer = $categorizer;
    }

    public function mount(Project $project): void
    {
        $this->project  = $project;
        $this->dateFrom = now()->toDateString();
        $this->dateTo   = now()->toDateString();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[On('project-filters-changed')]
    public function applyFilters(
        string $category = '',
        string $severity = '',
        string $dateFrom = '',
        string $dateTo   = '',
    ): void {
        $this->categoryFilter = $category;
        $this->severityFilter = $severity;
        $this->dateFrom       = $dateFrom;
        $this->dateTo         = $dateTo;
        $this->resetPage();
    }

    #[On('refresh-project')]
    public function refreshErrors(?int $projectId = null): void
    {
        if ($projectId !== null && $projectId !== $this->project->id) {
            return;
        }
        // Re-render is automatic
    }

    public function viewError(string $errorId): void
    {
        $errors      = $this->apiService->getTodayErrors($this->project);
        $categorized = collect($this->categorizer->categorizeCollection($errors));
        $this->selectedError = $categorized->firstWhere('id', $errorId);
    }

    public function closeModal(): void
    {
        $this->selectedError = null;
    }

    public function render(): \Illuminate\View\View
    {
        $errors      = $this->apiService->getTodayErrors($this->project);
        $categorized = collect($this->categorizer->categorizeCollection($errors));

        if ($this->categoryFilter) {
            $categorized = $categorized->where('category', $this->categoryFilter);
        }

        if ($this->severityFilter) {
            $categorized = $categorized->where('severity', $this->severityFilter);
        }

        if ($this->search) {
            $lower       = strtolower($this->search);
            $categorized = $categorized->filter(
                fn ($e) => str_contains(strtolower($e->message), $lower)
                        || str_contains(strtolower($e->exception), $lower)
            );
        }

        $sorted      = $categorized->sortByDesc('occurred_at');
        $total       = $sorted->count();
        $currentPage = $this->getPage();
        $items       = $sorted->forPage($currentPage, $this->perPage);

        return view('livewire.projects.project-errors-table', [
            'errors'   => $items,
            'total'    => $total,
            'perPage'  => $this->perPage,
        ]);
    }
}
