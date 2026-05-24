<div class="space-y-5">

    {{-- Date range --}}
    <div>
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Date Range</p>
        <div class="space-y-2">
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">From</label>
                <input wire:model.live="dateFrom" type="date"
                       class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">To</label>
                <input wire:model.live="dateTo" type="date"
                       class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
    </div>

    {{-- Severity --}}
    <div>
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Severity</p>
        <div class="space-y-1">
            @php
            $severityColors = [
                'critical' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 border-red-200 dark:border-red-700',
                'error'    => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300 border-orange-200 dark:border-orange-700',
                'warning'  => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300 border-yellow-200 dark:border-yellow-700',
                'info'     => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border-blue-200 dark:border-blue-700',
            ];
            @endphp
            @foreach($severities as $severity)
                <button wire:click="selectSeverity('{{ $severity }}')"
                        class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-lg border transition-colors
                               {{ $selectedSeverity === $severity
                                    ? $severityColors[$severity]
                                    : 'bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600' }}">
                    <span class="capitalize">{{ $severity }}</span>
                    @if($selectedSeverity === $severity)
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    {{-- Category --}}
    <div>
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Category</p>
        <div class="space-y-1">
            @foreach($categories as $category)
                <button wire:click="selectCategory('{{ $category }}')"
                        class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-lg transition-colors
                               {{ $selectedCategory === $category
                                    ? 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300'
                                    : 'bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-600' }}">
                    <span class="capitalize">{{ $category }}</span>
                    @if($selectedCategory === $category)
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    {{-- Clear all --}}
    @if($selectedCategory || $selectedSeverity)
        <button wire:click="clearAll"
                class="w-full px-3 py-2 text-sm text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
            Clear All Filters
        </button>
    @endif
</div>
