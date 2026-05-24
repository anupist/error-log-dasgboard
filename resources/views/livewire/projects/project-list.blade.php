<div>
    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Projects</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ $projects->total() }} project{{ $projects->total() !== 1 ? 's' : '' }} configured
            </p>
        </div>
        <button wire:click="openCreateModal"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Project
        </button>
    </div>

    {{-- ── Filters bar ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <div class="relative flex-1">
            <input wire:model.live.debounce.300ms="search"
                   type="text" placeholder="Search projects…"
                   class="w-full pl-10 pr-4 py-2.5 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900 dark:text-white placeholder-gray-400">
            <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <select wire:model.live="statusFilter"
                class="px-3 py-2.5 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-indigo-500">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
        <select wire:model.live="environmentFilter"
                class="px-3 py-2.5 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-indigo-500">
            <option value="">All Environments</option>
            <option value="production">Production</option>
            <option value="staging">Staging</option>
            <option value="development">Development</option>
        </select>
    </div>

    {{-- ── Project grid ─────────────────────────────────────────────────── --}}
    @if($projects->isEmpty())
        <div class="flex flex-col items-center justify-center py-24 text-gray-400 dark:text-gray-500">
            <svg class="w-16 h-16 mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <p class="text-lg font-medium">No projects found</p>
            <p class="text-sm mt-1">Add your first project to start monitoring.</p>
            <button wire:click="openCreateModal"
                    class="mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                Add Project
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($projects as $project)
                @livewire('projects.project-card', ['project' => $project], key($project->id))
            @endforeach
        </div>
        <div class="mt-8">{{ $projects->links() }}</div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- CREATE MODAL — always in DOM, shown via Livewire $showCreateModal  --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="{{ $showCreateModal ? '' : 'display:none' }}"
    >
        <div class="absolute inset-0 bg-black/50" wire:click="$set('showCreateModal', false)"></div>
        <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add New Project</h3>
                <button wire:click="$set('showCreateModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form wire:submit="createProject" class="px-6 py-5 space-y-4 max-h-[75vh] overflow-y-auto">
                @include('livewire.projects._project-form')
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showCreateModal', false)"
                            class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                        <span wire:loading.remove wire:target="createProject">Create Project</span>
                        <span wire:loading wire:target="createProject">Saving…</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- EDIT MODAL                                                         --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="{{ $showEditModal ? '' : 'display:none' }}"
    >
        <div class="absolute inset-0 bg-black/50" wire:click="$set('showEditModal', false)"></div>
        <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Project</h3>
                <button wire:click="$set('showEditModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form wire:submit="updateProject" class="px-6 py-5 space-y-4 max-h-[75vh] overflow-y-auto">
                @include('livewire.projects._project-form', ['isEdit' => true])
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showEditModal', false)"
                            class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                        <span wire:loading.remove wire:target="updateProject">Save Changes</span>
                        <span wire:loading wire:target="updateProject">Saving…</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- DELETE CONFIRM MODAL                                               --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="{{ $showDeleteModal ? '' : 'display:none' }}"
    >
        <div class="absolute inset-0 bg-black/50" wire:click="$set('showDeleteModal', false)"></div>
        <div class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 dark:bg-red-900/30 rounded-full">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-center text-lg font-semibold text-gray-900 dark:text-white mb-2">Delete Project</h3>
            <p class="text-center text-sm text-gray-500 dark:text-gray-400 mb-6">
                This action cannot be undone.
            </p>
            <div class="flex gap-3">
                <button wire:click="$set('showDeleteModal', false)"
                        class="flex-1 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Cancel
                </button>
                <button wire:click="deleteProject"
                        class="flex-1 px-4 py-2 text-sm text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                    <span wire:loading.remove wire:target="deleteProject">Delete</span>
                    <span wire:loading wire:target="deleteProject">Deleting…</span>
                </button>
            </div>
        </div>
    </div>
</div>
