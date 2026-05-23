<?php

namespace App\Livewire\Errors;

use Livewire\Component;

class ErrorSearch extends Component
{
    public $search = '';

    public function updatedSearch()
    {
        $this->dispatch('search-updated', search: $this->search);
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->dispatch('search-updated', search: '');
    }

    public function render()
    {
        return view('livewire.errors.error-search');
    }
}
