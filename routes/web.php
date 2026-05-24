<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Projects\ProjectList;
use App\Livewire\Projects\ProjectDashboard;

// ── Project routes ────────────────────────────────────────────────────────────
Route::get('/', ProjectList::class)->name('projects.index');
Route::get('/projects/{project:slug}', ProjectDashboard::class)->name('projects.show');
