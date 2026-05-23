<div>
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Exception</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Message</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Severity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($errors as $error)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $error->occurred_at->format('H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ $error->exception }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                            <div class="max-w-md truncate" title="{{ $error->message }}">
                                {{ $error->getShortMessage(80) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 capitalize">
                                {{ $error->category ?? 'general' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $severityClass = match($error->severity) {
                                    'critical' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    'error' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                    'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    default => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $severityClass }} capitalize">
                                {{ $error->severity ?? 'info' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <button 
                                wire:click="viewError('{{ $error->id }}')"
                                class="text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 font-medium"
                            >
                                View Details
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-lg font-medium">No errors found</p>
                                <p class="text-sm">Try adjusting your filters or search query</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($errors->count() > 0 && $total > $perPage)
        <div class="mt-6 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Showing {{ ($errors->count() > 0) ? (($this->getPage() - 1) * $perPage + 1) : 0 }} 
                to {{ min($this->getPage() * $perPage, $total) }} 
                of {{ $total }} errors
            </div>
            <div class="flex space-x-2">
                @if($this->getPage() > 1)
                    <button wire:click="previousPage" class="px-3 py-1 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-600">
                        Previous
                    </button>
                @endif
                
                @php
                    $lastPage = ceil($total / $perPage);
                    $currentPage = $this->getPage();
                @endphp
                
                @for($i = max(1, $currentPage - 2); $i <= min($lastPage, $currentPage + 2); $i++)
                    <button 
                        wire:click="gotoPage({{ $i }})" 
                        class="px-3 py-1 text-sm {{ $i == $currentPage ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600' }} rounded"
                    >
                        {{ $i }}
                    </button>
                @endfor
                
                @if($this->getPage() < $lastPage)
                    <button wire:click="nextPage" class="px-3 py-1 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-600">
                        Next
                    </button>
                @endif
            </div>
        </div>
    @elseif($errors->count() > 0)
        <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Showing {{ $errors->count() }} of {{ $total }} errors
            </div>
        </div>
    @endif

    <!-- Error Detail Modal -->
    @if($selectedError)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: true }" x-show="show" x-transition>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" @click="$wire.closeModal()"></div>

                <!-- Modal panel -->
                <div class="inline-block w-full max-w-4xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Error Details</h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="px-6 py-4 space-y-4 max-h-[70vh] overflow-y-auto">
                        <!-- Exception -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Exception</label>
                            <p class="text-sm text-gray-900 dark:text-white font-mono bg-gray-50 dark:bg-gray-900 p-3 rounded">{{ $selectedError->exception }}</p>
                        </div>

                        <!-- Message -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message</label>
                            <p class="text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-900 p-3 rounded">{{ $selectedError->message }}</p>
                        </div>

                        <!-- File & Line -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">File</label>
                                <p class="text-sm text-gray-900 dark:text-white font-mono bg-gray-50 dark:bg-gray-900 p-3 rounded break-all">{{ $selectedError->file }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Line</label>
                                <p class="text-sm text-gray-900 dark:text-white font-mono bg-gray-50 dark:bg-gray-900 p-3 rounded">{{ $selectedError->line }}</p>
                            </div>
                        </div>

                        <!-- Category & Severity -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 capitalize">{{ $selectedError->category ?? 'general' }}</span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Severity</label>
                                @php
                                    $severityClass = match($selectedError->severity) {
                                        'critical' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                        'error' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                        'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        default => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $severityClass }} capitalize">{{ $selectedError->severity ?? 'info' }}</span>
                            </div>
                        </div>

                        <!-- Timestamp -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Occurred At</label>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $selectedError->occurred_at->format('Y-m-d H:i:s') }}</p>
                        </div>

                        <!-- Stack Trace -->
                        @if($selectedError->trace)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stack Trace</label>
                                <pre class="text-xs text-gray-900 dark:text-white font-mono bg-gray-50 dark:bg-gray-900 p-3 rounded overflow-x-auto">{{ $selectedError->trace }}</pre>
                            </div>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        <button wire:click="closeModal" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 rounded-lg transition-colors duration-200">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
