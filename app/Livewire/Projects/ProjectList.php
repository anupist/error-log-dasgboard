<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectList extends Component
{
    use WithPagination;

    public string $search            = '';
    public string $statusFilter      = '';
    public string $environmentFilter = '';

    // ─── Modal state ─────────────────────────────────────────────────────────
    public bool $showCreateModal = false;
    public bool $showEditModal   = false;
    public bool $showDeleteModal = false;
    public ?int $editingId       = null;
    public ?int $deletingId      = null;

    // ─── Form fields ─────────────────────────────────────────────────────────
    public string $form_name        = '';
    public string $form_slug        = '';
    public string $form_api_url     = '';
    public string $form_api_token   = '';
    public string $form_environment = 'production';
    public string $form_status      = 'active';
    public string $form_notes       = '';

    protected array $rules = [
        'form_name'        => 'required|string|max:100',
        'form_slug'        => 'required|string|max:100|regex:/^[a-z0-9\-]+$/',
        'form_api_url'     => 'required|url|max:500',
        'form_api_token'   => 'nullable|string|max:500',
        'form_environment' => 'required|in:production,staging,development',
        'form_status'      => 'required|in:active,inactive',
        'form_notes'       => 'nullable|string|max:1000',
    ];

    protected array $messages = [
        'form_slug.regex' => 'Slug may only contain lowercase letters, numbers, and hyphens.',
    ];

    // ─── Lifecycle ───────────────────────────────────────────────────────────

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /** Auto-generate slug from name when creating */
    public function updatedFormName(string $value): void
    {
        if (! $this->editingId) {
            $this->form_slug = Str::slug($value);
        }
    }

    // ─── Event listeners from ProjectCard (dispatched via Alpine $dispatch) ──

    #[On('open-edit-modal')]
    public function handleOpenEditModal(int $id): void
    {
        $this->openEditModal($id);
    }

    #[On('confirm-delete')]
    public function handleConfirmDelete(int $id): void
    {
        $this->confirmDelete($id);
    }

    // ─── Create ──────────────────────────────────────────────────────────────

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function createProject(): void
    {
        $this->validate();

        Project::create([
            'name'        => $this->form_name,
            'slug'        => $this->form_slug,
            'api_url'     => $this->form_api_url,
            'api_token'   => $this->form_api_token ?: null,
            'environment' => $this->form_environment,
            'status'      => $this->form_status,
            'notes'       => $this->form_notes ?: null,
        ]);

        $this->showCreateModal = false;
        $this->resetForm();
        session()->flash('success', 'Project created successfully.');
    }

    // ─── Edit ────────────────────────────────────────────────────────────────

    public function openEditModal(int $id): void
    {
        $project = Project::findOrFail($id);

        $this->editingId        = $id;
        $this->form_name        = $project->name;
        $this->form_slug        = $project->slug;
        $this->form_api_url     = $project->api_url;
        $this->form_api_token   = '';   // never pre-fill token
        $this->form_environment = $project->environment;
        $this->form_status      = $project->status;
        $this->form_notes       = $project->notes ?? '';
        $this->showEditModal    = true;
    }

    public function updateProject(): void
    {
        $this->validate([
            'form_name'        => 'required|string|max:100',
            'form_slug'        => "required|string|max:100|regex:/^[a-z0-9\\-]+$/|unique:projects,slug,{$this->editingId}",
            'form_api_url'     => 'required|url|max:500',
            'form_api_token'   => 'nullable|string|max:500',
            'form_environment' => 'required|in:production,staging,development',
            'form_status'      => 'required|in:active,inactive',
            'form_notes'       => 'nullable|string|max:1000',
        ]);

        $project = Project::findOrFail($this->editingId);

        $data = [
            'name'        => $this->form_name,
            'slug'        => $this->form_slug,
            'api_url'     => $this->form_api_url,
            'environment' => $this->form_environment,
            'status'      => $this->form_status,
            'notes'       => $this->form_notes ?: null,
        ];

        if ($this->form_api_token) {
            $data['api_token'] = $this->form_api_token;
        }

        $project->update($data);

        $this->showEditModal = false;
        $this->editingId     = null;
        $this->resetForm();
        session()->flash('success', 'Project updated successfully.');
    }

    // ─── Delete ──────────────────────────────────────────────────────────────

    public function confirmDelete(int $id): void
    {
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function deleteProject(): void
    {
        Project::findOrFail($this->deletingId)->delete();
        $this->showDeleteModal = false;
        $this->deletingId      = null;
        session()->flash('success', 'Project deleted.');
    }

    // ─── Toggle status ───────────────────────────────────────────────────────

    public function toggleStatus(int $id): void
    {
        $project         = Project::findOrFail($id);
        $project->status = $project->status === 'active' ? 'inactive' : 'active';
        $project->save();
    }

    // ─── Render ──────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        $projects = Project::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->environmentFilter, fn ($q) => $q->where('environment', $this->environmentFilter))
            ->orderBy('name')
            ->paginate(12);

        return view('livewire.projects.project-list', compact('projects'))
            ->layout('layouts.app', [
                'header'    => 'Projects',
                'subheader' => 'Manage and monitor all your projects',
            ]);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->form_name        = '';
        $this->form_slug        = '';
        $this->form_api_url     = '';
        $this->form_api_token   = '';
        $this->form_environment = 'production';
        $this->form_status      = 'active';
        $this->form_notes       = '';
        $this->resetValidation();
    }
}
