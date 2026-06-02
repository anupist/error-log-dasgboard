<?php

use App\Http\Controllers\Auth\LoginController;
use App\Livewire\Projects\ProjectList;
use App\Livewire\Projects\ProjectDashboard;
use Illuminate\Support\Facades\Route;

// ── Auth routes (guests only) ─────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Protected routes ──────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/', ProjectList::class)->name('projects.index');
    Route::get('/projects/{project:slug}', ProjectDashboard::class)->name('projects.show');
});
