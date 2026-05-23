<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard\DashboardOverview;

Route::get('/', DashboardOverview::class)->name('dashboard');
