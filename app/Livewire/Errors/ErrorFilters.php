<?php

namespace App\Livewire\Errors;

use App\Services\ErrorAnalyzer\ErrorCategorizer;
use Livewire\Component;

class ErrorFilters extends Component
{
    public $selectedCategory = '';
    public $categories = [];

    protected $errorCategorizer;

    public function boot(ErrorCategorizer $errorCategorizer)
    {
        $this->errorCategorizer = $errorCategorizer;
    }

    public function mount()
    {
        $this->categories = $this->errorCategorizer->getCategories();
    }

    public function selectCategory($category)
    {
        $this->selectedCategory = $category === $this->selectedCategory ? '' : $category;
        $this->dispatch('filter-changed', category: $this->selectedCategory);
    }

    public function clearFilter()
    {
        $this->selectedCategory = '';
        $this->dispatch('filter-changed', category: '');
    }

    public function render()
    {
        return view('livewire.errors.error-filters');
    }
}
