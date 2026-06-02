<div wire:poll.{{ $refreshInterval }}s="refreshData" wire:init="@if($selectedLogFile)selectLogFile('{{ $selectedLogFile }}')@endif" class="space-y-6">

    {{-- ── Breadcrumb + actions ─────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('projects.index') }}"
                   class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    Projects
                </a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900 dark:text-white font-medium">{{ e($project->name) }}</span>
            </nav>

            <div class="flex items-center gap-3">
                @php
                    $envColor = match($project->environment) {
                        'production'  => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                        'staging'     => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
                        'development' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                        default       => 'bg-gray-100 text-gray-600',
                    };
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $envColor }} capitalize">
                    {{ $project->environment }}
                </span>
                @if($project->notes)
                    <span class="text-sm text-gray-500 dark:text-gray-400 italic">{{ e($project->notes) }}</span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-500 dark:text-gray-400">
                Auto-refresh every {{ $refreshInterval }}s
            </span>
            <button wire:click="refreshData"
                    class="inline-flex items-center px-3 py-2 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </button>
        </div>
    </div>

    {{-- ── Log File Selector ────────────────────────────────────────────── --}}
    @if(!empty($logFiles))
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <label class="text-sm font-semibold text-gray-900 dark:text-white">Select Log File</label>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ count($logFiles) }} file(s) available</span>
            </div>
            <select wire:change="selectLogFile($event.target.value)"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                @foreach($logFiles as $file)
                    <option value="{{ $file['filename'] }}" @selected($selectedLogFile === $file['filename'])>
                        {{ $file['filename'] }} ({{ $file['size_human'] }}) - {{ $file['last_modified'] }}
                    </option>
                @endforeach
            </select>
        </div>
    @else
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-yellow-800 dark:text-yellow-200">No Log Files Found</p>
                    <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">The API returned no log files for this project.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Stats cards ──────────────────────────────────────────────────── --}}
    @livewire('projects.project-stats', ['project' => $project], key('stats-'.$project->id))

    {{-- ── Charts ───────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">
                Error Trend
                @if($selectedLogFile)
                    <span class="text-xs font-normal text-gray-400 ml-2">— {{ $selectedLogFile }}</span>
                @endif
            </h3>
            @livewire('projects.project-charts', ['project' => $project, 'chartType' => 'trend'], key('charts-trend-'.$project->id))
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Category Distribution</h3>
            @livewire('projects.project-charts', ['project' => $project, 'chartType' => 'category'], key('charts-category-'.$project->id))
        </div>
    </div>

    {{-- ── Filters + Table ──────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Filters</h3>
                @livewire('projects.project-filters', ['project' => $project], key('filters-'.$project->id))
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                @livewire('projects.project-errors-table', ['project' => $project], key('table-'.$project->id))
            </div>
        </div>
    </div>
</div>
