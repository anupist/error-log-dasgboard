<div
    wire:init="loadStats"
    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col"
>
    {{-- ── Header ───────────────────────────────────────────────────────── --}}
    <div class="px-5 pt-5 pb-4 flex items-start justify-between">
        <div class="flex-1 min-w-0">
            {{-- Environment badge --}}
            @php
                $envColor = match($project->environment) {
                    'production'  => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                    'staging'     => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
                    'development' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                    default       => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                };
            @endphp
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $envColor }} capitalize">
                    {{ $project->environment }}
                </span>
                @if($project->status === 'inactive')
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                        Inactive
                    </span>
                @endif
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate">{{ e($project->name) }}</h3>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 truncate">{{ $project->slug }}</p>
        </div>

        {{-- Three-dot menu — Alpine dropdown, dispatches to Livewire via $wire --}}
        <div x-data="{ open: false }" class="relative ml-2 shrink-0">
            <button
                type="button"
                @click="open = !open"
                @click.outside="open = false"
                class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                </svg>
            </button>

            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-1 w-44 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 py-1 z-20"
                style="display:none"
            >
                {{-- View Dashboard: plain <a> tag, always works --}}
                <a
                    href="{{ route('projects.show', $project) }}"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                    <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    View Dashboard
                </a>

                {{-- Edit: wire:click on the card component, which dispatches to parent --}}
                <button
                    type="button"
                    wire:click="triggerEdit"
                    @click="open = false"
                    class="w-full flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                    <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </button>

                {{-- Delete: wire:click on the card component, which dispatches to parent --}}
                <button
                    type="button"
                    wire:click="triggerDelete"
                    @click="open = false"
                    class="w-full flex items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20"
                >
                    <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete
                </button>
            </div>
        </div>
    </div>

    {{-- ── Stats ────────────────────────────────────────────────────────── --}}
    <div class="px-5 pb-4 grid grid-cols-2 gap-3">
        @if($loading)
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 animate-pulse">
                <div class="h-3 bg-gray-200 dark:bg-gray-600 rounded w-16 mb-2"></div>
                <div class="h-6 bg-gray-200 dark:bg-gray-600 rounded w-10"></div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 animate-pulse">
                <div class="h-3 bg-gray-200 dark:bg-gray-600 rounded w-16 mb-2"></div>
                <div class="h-6 bg-gray-200 dark:bg-gray-600 rounded w-10"></div>
            </div>
        @else
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
                <p class="text-xs text-blue-600 dark:text-blue-400 font-medium">Total Errors</p>
                <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 mt-0.5">{{ number_format($totalErrors) }}</p>
            </div>
            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3">
                <p class="text-xs text-red-600 dark:text-red-400 font-medium">Critical</p>
                <p class="text-2xl font-bold text-red-700 dark:text-red-300 mt-0.5">{{ number_format($criticalErrors) }}</p>
            </div>
        @endif
    </div>

    {{-- ── Footer ───────────────────────────────────────────────────────── --}}
    <div class="mt-auto px-5 py-3 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
        <div class="flex items-center gap-1.5 text-xs">
            @if($loading)
                <span class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-600 animate-pulse"></span>
                <span class="text-gray-400">Checking…</span>
            @elseif($apiOnline)
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-green-600 dark:text-green-400">Online</span>
                @if($responseTimeMs !== null)
                    <span class="text-gray-400 dark:text-gray-500">· {{ $responseTimeMs }}ms</span>
                @endif
            @else
                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                <span class="text-red-600 dark:text-red-400">Offline</span>
            @endif
        </div>
        <a href="{{ route('projects.show', $project) }}"
           class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
            View Dashboard →
        </a>
    </div>
</div>
