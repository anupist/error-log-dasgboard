<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
    x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
    :class="{ 'dark': darkMode }"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ErrorWatch') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-gray-50 dark:bg-gray-900">

{{-- NOTE: id="app" is NOT used here — Vue mounts on [data-vue-chart] elements only --}}
<div class="min-h-screen flex">

    {{-- ── Sidebar ─────────────────────────────────────────────────────── --}}
    <aside class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 z-50 flex flex-col">

        {{-- Logo --}}
        <div class="flex items-center h-16 px-6 border-b border-gray-200 dark:border-gray-700 shrink-0">
            <a href="{{ route('projects.index') }}" class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <span class="text-xl font-bold text-gray-900 dark:text-white">ErrorWatch</span>
            </a>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-1">

            <a href="{{ route('projects.index') }}"
               class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                      {{ request()->routeIs('projects.index')
                            ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                All Projects
            </a>

            @php $sidebarProjects = \App\Models\Project::active()->orderBy('name')->get(); @endphp

            @if($sidebarProjects->isNotEmpty())
                <div class="pt-3 pb-1">
                    <p class="px-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Projects</p>
                </div>

                @foreach($sidebarProjects as $sp)
                    <a href="{{ route('projects.show', $sp) }}"
                       class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
                              {{ request()->route('project') && request()->route('project')->is($sp)
                                    ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300'
                                    : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                        <span class="w-2 h-2 rounded-full mr-3 shrink-0
                                     {{ $sp->environment === 'production' ? 'bg-red-400' : ($sp->environment === 'staging' ? 'bg-yellow-400' : 'bg-green-400') }}"></span>
                        <span class="truncate">{{ $sp->name }}</span>
                    </a>
                @endforeach
            @endif
        </nav>

        {{-- Footer --}}
        <div class="p-4 border-t border-gray-200 dark:border-gray-700 shrink-0">
            {{-- Logged-in user --}}
            @auth
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center shrink-0">
                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            @endauth

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400 dark:text-gray-500">v2.0.0</span>

                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="text-xs text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 transition-colors flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Sign out
                            </button>
                        </form>
                    @endauth
                </div>

                <button @click="darkMode = !darkMode"
                        class="p-2 rounded-lg text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>
            </div>
        </div>
    </aside>

    {{-- ── Main area ───────────────────────────────────────────────────── --}}
    <div class="pl-64 flex-1 flex flex-col min-h-screen">

        <header class="sticky top-0 z-40 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between h-16 px-8">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $header ?? 'Error Monitoring Dashboard' }}
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $subheader ?? 'Real-time multi-project error tracking' }}
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="flex items-center space-x-1.5 text-xs text-gray-500 dark:text-gray-400">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                        <span>Live</span>
                    </div>
                </div>
            </div>
        </header>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="mx-8 mt-4 px-4 py-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-300 flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="ml-4 text-green-600 hover:text-green-800">✕</button>
            </div>
        @endif

        <main class="flex-1 p-8">
            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
{{-- DO NOT add Alpine CDN here — Livewire 4 already bundles Alpine internally --}}
</body>
</html>
