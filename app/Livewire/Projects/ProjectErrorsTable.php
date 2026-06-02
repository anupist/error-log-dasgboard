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

    public string  $search         = '';
    public string  $categoryFilter = '';
    public string  $severityFilter = '';
    public int     $perPage        = 20;
    public string  $currentLogFile = '';

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
        $this->project = $project;
        // Table starts empty; ProjectDashboard fires log-file-changed after mount
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
        $this->resetPage();
    }

    #[On('log-file-changed')]
    public function onLogFileChanged(int $projectId, string $logFile): void
    {
        if ($projectId !== $this->project->id) {
            return;
        }

        $this->currentLogFile = $logFile;
        $this->resetPage();
    }

    public function viewError(string $errorId): void
    {
        if (empty($this->currentLogFile)) {
            return;
        }

        $errors      = $this->apiService->getErrorsByLogFile($this->project, $this->currentLogFile);
        $categorized = collect($this->categorizer->categorizeCollection($errors));
        $this->selectedError = $categorized->firstWhere('id', $errorId);
    }

    public function closeModal(): void
    {
        $this->selectedError = null;
    }

    public function render(): \Illuminate\View\View
    {
        $categorized = collect();

        if ($this->currentLogFile) {
            $errors      = $this->apiService->getErrorsByLogFile($this->project, $this->currentLogFile);
            $categorized = collect($this->categorizer->categorizeCollection($errors));
        }

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
            'errors'  => $items,
            'total'   => $total,
            'perPage' => $this->perPage,
        ]);
    }
}
