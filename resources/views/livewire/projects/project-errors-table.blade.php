<div>
    {{-- Search bar --}}
    <div class="relative mb-4">
        <input wire:model.live.debounce.400ms="search"
               type="text" placeholder="Search by message or exception…"
               class="w-full pl-10 pr-10 py-2.5 text-sm bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white placeholder-gray-400">
        <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        @if($search)
            <button wire:click="$set('search', '')"
                    class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        @endif
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Time</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Exception</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Message</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Severity</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($errors as $error)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400 font-mono">
                            {{ $error->occurred_at->format('H:i:s') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white max-w-[180px] truncate">
                            {{ e($error->exception) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 max-w-xs">
                            <span class="line-clamp-1" title="{{ e($error->message) }}">
                                {{ e($error->getShortMessage(80)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 capitalize">
                                {{ $error->category ?? 'general' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $sc = match($error->severity) {
                                    'critical' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                                    'error'    => 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
                                    'warning'  => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                                    default    => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $sc }} capitalize">
                                {{ $error->severity ?? 'info' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            <button wire:click="viewError('{{ e($error->id) }}')"
                                    class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                                Details
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center text-gray-400 dark:text-gray-500">
                                <svg class="w-10 h-10 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="font-medium">No errors found</p>
                                <p class="text-sm mt-1">Try adjusting your filters or search query.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($total > $perPage)
        <div class="mt-4 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
            <span>
                Showing {{ ($this->getPage() - 1) * $perPage + 1 }}–{{ min($this->getPage() * $perPage, $total) }}
                of {{ $total }}
            </span>
            <div class="flex items-center gap-1">
                @if($this->getPage() > 1)
                    <button wire:click="previousPage"
                            class="px-3 py-1.5 text-xs bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        ← Prev
                    </button>
                @endif

                @php
                    $lastPage    = (int) ceil($total / $perPage);
                    $currentPage = $this->getPage();
                    $start       = max(1, $currentPage - 2);
                    $end         = min($lastPage, $currentPage + 2);
                @endphp

                @for($i = $start; $i <= $end; $i++)
                    <button wire:click="gotoPage({{ $i }})"
                            class="px-3 py-1.5 text-xs rounded-lg transition-colors
                                   {{ $i === $currentPage
                                        ? 'bg-indigo-600 text-white'
                                        : 'bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                        {{ $i }}
                    </button>
                @endfor

                @if($currentPage < $lastPage)
                    <button wire:click="nextPage"
                            class="px-3 py-1.5 text-xs bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        Next →
                    </button>
                @endif
            </div>
        </div>
    @elseif($total > 0)
        <div class="mt-3 text-xs text-gray-400 dark:text-gray-500">
            Showing all {{ $total }} error{{ $total !== 1 ? 's' : '' }}
        </div>
    @endif

    {{-- ── Error detail modal ───────────────────────────────────────────── --}}
    @if($selectedError)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data x-init="document.body.style.overflow='hidden'"
             x-destroy="document.body.style.overflow=''">
            <div class="absolute inset-0 bg-black/50" wire:click="closeModal"></div>
            <div class="relative w-full max-w-3xl bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 shrink-0">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Error Details</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="overflow-y-auto px-6 py-5 space-y-4 flex-1">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Exception</p>
                            <p class="text-sm font-mono bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white p-3 rounded-lg break-all">{{ e($selectedError->exception) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Occurred At</p>
                            <p class="text-sm bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white p-3 rounded-lg">{{ $selectedError->occurred_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Message</p>
                        <p class="text-sm bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white p-3 rounded-lg">{{ e($selectedError->message) }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">File</p>
                            <p class="text-xs font-mono bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white p-3 rounded-lg break-all">{{ e($selectedError->file) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Line</p>
                            <p class="text-sm font-mono bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white p-3 rounded-lg">{{ $selectedError->line }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Category</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 capitalize">
                                {{ $selectedError->category ?? 'general' }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Severity</p>
                            @php
                                $sc = match($selectedError->severity) {
                                    'critical' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                                    'error'    => 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
                                    'warning'  => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                                    default    => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sc }} capitalize">
                                {{ $selectedError->severity ?? 'info' }}
                            </span>
                        </div>
                    </div>

                    @if($selectedError->trace)
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Stack Trace</p>
                            <pre class="text-xs font-mono bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto max-h-64 overflow-y-auto">{{ e($selectedError->trace) }}</pre>
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end shrink-0">
                    <button wire:click="closeModal"
                            class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
