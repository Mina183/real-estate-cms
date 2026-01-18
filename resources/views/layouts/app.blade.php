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

        <!-- Select2 -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white">
    <button id="darkModeToggle" class="fixed bottom-4 right-4 bg-[#0e2442] text-white text-sm px-2 py-1 rounded shadow z-50">
        Toggle Dark Mode
    </button>
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-gradient-to-br from-brand-accent-light/60 via-brand-light/40 to-gray-50 shadow-md">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <script src="https://unpkg.com/alpinejs" defer></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const toggle = document.getElementById('darkModeToggle');
                const root = document.documentElement;

                // Enable dark mode if stored
                if (localStorage.getItem('theme') === 'dark') {
                    root.classList.add('dark');
                }

                toggle?.addEventListener('click', () => {
                    root.classList.toggle('dark');
                    const mode = root.classList.contains('dark') ? 'dark' : 'light';
                    localStorage.setItem('theme', mode);
                });
            });
        </script>
    </body>
</html>

<style>[x-cloak] { display: none !important; }</style>
