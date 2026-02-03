<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <script>
            // Dark mode toggle
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <div class="max-w-full mx-auto flex gap-0 h-full">
                {{-- Sidebar (visible on large screens) --}}
                <div id="sidebar" class="hidden lg:block w-64 transition-all duration-300 relative flex-shrink-0">
                    <button id="sidebar-toggle" class="absolute -right-3 top-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full p-1.5 shadow-md hover:shadow-lg transition-all z-10">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    @include('layouts.sidebar')
                </div>

                <style>
                    #sidebar.collapsed {
                        width: 5rem;
                    }
                    #sidebar.collapsed .sidebar-text,
                    #sidebar.collapsed .text-xs {
                        opacity: 0;
                        width: 0;
                        display: none;
                    }
                    #sidebar.collapsed #sidebar-toggle svg {
                        transform: rotate(180deg);
                    }
                    /* Ensure icons are centered when collapsed */
                    #sidebar.collapsed a .flex {
                        justify-content: center;
                    }
                    #sidebar.collapsed a .mr-3 {
                        margin-right: 0;
                    }
                </style>

                <div class="flex-1 py-6 px-4 sm:px-6 lg:px-8 overflow-y-auto">
                    <!-- Page Heading -->
                    @isset($header)
                        <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="bg-white dark:bg-gray-800 py-6 px-4 sm:px-6 lg:px-8 shadow">
                        {{ $header }}
                    </div>
                        </header>
                    @endisset

                    <!-- Page Content -->
                    <main class="bg-white dark:bg-gray-800 shadow p-6">
                       @yield('content')
                       @isset($slot)
                           {{ $slot }}
                       @endisset
                    </main>
                </div>
            </div>
        </div>
        
        <script>
            // Dark mode toggle functionality
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
            const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

            // Show correct icon on page load
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                themeToggleLightIcon.classList.remove('hidden');
            } else {
                themeToggleDarkIcon.classList.remove('hidden');
            }

            themeToggleBtn.addEventListener('click', function() {
                // Toggle icons
                themeToggleDarkIcon.classList.toggle('hidden');
                themeToggleLightIcon.classList.toggle('hidden');

                // Toggle dark mode
                if (localStorage.getItem('color-theme')) {
                    if (localStorage.getItem('color-theme') === 'light') {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('color-theme', 'dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('color-theme', 'light');
                    }
                } else {
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('color-theme', 'light');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('color-theme', 'dark');
                    }
                }
            });

            // Sidebar collapse functionality
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    const isCollapsed = sidebar.classList.contains('collapsed');
                    localStorage.setItem('sidebar-collapsed', isCollapsed);
                });

                // Restore sidebar state
                if (localStorage.getItem('sidebar-collapsed') === 'true') {
                    sidebar.classList.add('collapsed');
                }
            }
        </script>
        @yield('scripts')
    </body>
</html>
