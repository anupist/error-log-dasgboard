<div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-4">

    @php
    $cards = [
        ['label' => 'Total',      'value' => $totalErrors,      'color' => 'blue',   'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        ['label' => 'Critical',   'value' => $criticalErrors,   'color' => 'red',    'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'PHP',        'value' => $phpErrors,        'color' => 'purple', 'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'],
        ['label' => 'Database',   'value' => $dbErrors,         'color' => 'orange', 'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
        ['label' => 'Validation', 'value' => $validationErrors, 'color' => 'yellow', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'API',        'value' => $apiErrors,        'color' => 'teal',   'icon' => 'M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ['label' => 'Queue',      'value' => $queueErrors,      'color' => 'indigo', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'],
        ['label' => 'Rate/hr',    'value' => $errorRate,        'color' => 'gray',   'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
    ];

    $colorMap = [
        'blue'   => ['bg' => 'bg-blue-50 dark:bg-blue-900/20',   'text' => 'text-blue-700 dark:text-blue-300',   'icon' => 'text-blue-500'],
        'red'    => ['bg' => 'bg-red-50 dark:bg-red-900/20',     'text' => 'text-red-700 dark:text-red-300',     'icon' => 'text-red-500'],
        'purple' => ['bg' => 'bg-purple-50 dark:bg-purple-900/20','text' => 'text-purple-700 dark:text-purple-300','icon' => 'text-purple-500'],
        'orange' => ['bg' => 'bg-orange-50 dark:bg-orange-900/20','text' => 'text-orange-700 dark:text-orange-300','icon' => 'text-orange-500'],
        'yellow' => ['bg' => 'bg-yellow-50 dark:bg-yellow-900/20','text' => 'text-yellow-700 dark:text-yellow-300','icon' => 'text-yellow-500'],
        'teal'   => ['bg' => 'bg-teal-50 dark:bg-teal-900/20',   'text' => 'text-teal-700 dark:text-teal-300',   'icon' => 'text-teal-500'],
        'indigo' => ['bg' => 'bg-indigo-50 dark:bg-indigo-900/20','text' => 'text-indigo-700 dark:text-indigo-300','icon' => 'text-indigo-500'],
        'gray'   => ['bg' => 'bg-gray-50 dark:bg-gray-700/40',   'text' => 'text-gray-700 dark:text-gray-300',   'icon' => 'text-gray-500'],
    ];
    @endphp

    @foreach($cards as $card)
        @php $c = $colorMap[$card['color']]; @endphp
        <div class="{{ $c['bg'] }} rounded-xl p-4 flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
                <svg class="w-4 h-4 {{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
            <p class="text-2xl font-bold {{ $c['text'] }}">{{ $card['value'] }}</p>
        </div>
    @endforeach
</div>
