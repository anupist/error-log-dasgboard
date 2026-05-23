<?php

namespace App\Livewire\Dashboard;

use App\Services\Api\ErrorApiService;
use App\Services\ErrorAnalyzer\ErrorCategorizer;
use Livewire\Component;

class DashboardOverview extends Component
{
    public $refreshInterval;

    public function mount()
    {
        $this->refreshInterval = config('error-api.auto_refresh', 30);
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-overview')
            ->layout('layouts.app');
    }

    public function refreshData()
    {
        $this->dispatch('refresh-dashboard');
    }
}
