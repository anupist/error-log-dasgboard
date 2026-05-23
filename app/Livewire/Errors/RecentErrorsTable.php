<?php

namespace App\Livewire\Errors;

use App\Services\Api\ErrorApiService;
use App\Services\ErrorAnalyzer\ErrorCategorizer;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class RecentErrorsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $selectedError = null;
    public $perPage = 20;

    protected $errorApiService;
    protected $errorCategorizer;

    public function boot(ErrorApiService $errorApiService, ErrorCategorizer $errorCategorizer)
    {
        $this->errorApiService = $errorApiService;
        $this->errorCategorizer = $errorCategorizer;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    #[On('filter-changed')]
    public function updateFilter($category)
    {
        $this->categoryFilter = $category;
        $this->resetPage();
    }

    #[On('search-updated')]
    public function updateSearch($search)
    {
        $this->search = $search;
        $this->resetPage();
    }

    #[On('refresh-dashboard')]
    public function refreshErrors()
    {
        // Just re-render
    }

    public function viewError($errorId)
    {
        $errors = $this->errorApiService->getTodayErrors();
        $categorizedErrors = collect($this->errorCategorizer->categorizeCollection($errors));
        
        $this->selectedError = $categorizedErrors->firstWhere('id', $errorId);
    }

    public function closeModal()
    {
        $this->selectedError = null;
    }

    public function render()
    {
        $errors = $this->errorApiService->getTodayErrors();
        $categorizedErrors = collect($this->errorCategorizer->categorizeCollection($errors));

        // Apply filters
        if ($this->categoryFilter) {
            $categorizedErrors = $categorizedErrors->where('category', $this->categoryFilter);
        }

        if ($this->search) {
            $categorizedErrors = $categorizedErrors->filter(function ($error) {
                return str_contains(strtolower($error->message), strtolower($this->search)) ||
                       str_contains(strtolower($error->exception), strtolower($this->search));
            });
        }

        // Sort and paginate
        $categorizedErrors = $categorizedErrors->sortByDesc('occurred_at');
        
        $currentPage = $this->getPage();
        $items = $categorizedErrors->forPage($currentPage, $this->perPage);
        $total = $categorizedErrors->count();

        return view('livewire.errors.recent-errors-table', [
            'errors' => $items,
            'total' => $total,
            'perPage' => $this->perPage,
        ]);
    }
}
