<div class="space-y-3">
    @if($selectedCategory)
        <button wire:click="clearFilter" class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 rounded-lg transition-colors duration-200 text-sm">
            Clear Filter
        </button>
    @endif

    <div class="space-y-2">
        @foreach($categories as $category)
            <button 
                wire:click="selectCategory('{{ $category }}')"
                class="w-full px-4 py-2 text-left text-sm rounded-lg transition-colors {{ $selectedCategory === $category ? 'bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 font-medium' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
            >
                <div class="flex items-center justify-between">
                    <span class="capitalize">{{ $category }}</span>
                    @if($selectedCategory === $category)
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </div>
            </button>
        @endforeach
    </div>
</div>
