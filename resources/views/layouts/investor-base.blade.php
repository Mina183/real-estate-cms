<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Investor Portal</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white">
    <button id="darkModeToggle" class="fixed bottom-4 right-4 bg-[#0e2442] text-white text-sm px-2 py-1 rounded shadow z-50">
        Toggle Dark Mode
    </button>
    
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Investor Navigation -->
        <nav x-data="{ open: false }" class="bg-brand-darker border-b border-brand-dark dark:border-brand-dark">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 min-h-[80px] py-4">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('investor.dashboard') }}">
                                <img src="/images/logo.png" alt="Logo" class="h-20 w-auto">
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('investor.dashboard') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out text-white hover:text-brand-accent
                                      {{ request()->routeIs('investor.dashboard') ? 'border-brand-accent' : 'border-transparent' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('investor.documents') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out text-white hover:text-brand-accent
                                      {{ request()->routeIs('investor.documents') ? 'border-brand-accent' : 'border-transparent' }}">
                                Documents
                            </a>
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = ! open" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::guard('investor')->user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>

                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5">
                                <a href="{{ route('investor.profile') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Profile
                                </a>
                                <form method="POST" action="{{ route('investor.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-me-2 flex items-center sm:hidden">
                        <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-brand-light hover:text-white hover:bg-brand-dark/20 focus:outline-none transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('investor.dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out text-white hover:bg-brand-dark/20 {{ request()->routeIs('investor.dashboard') ? 'border-brand-accent bg-brand-dark/20' : 'border-transparent' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('investor.documents') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out text-white hover:bg-brand-dark/20 {{ request()->routeIs('investor.documents') ? 'border-brand-accent bg-brand-dark/20' : 'border-transparent' }}">
                        Documents
                    </a>
                </div>

                <!-- Responsive Settings Options -->
                <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                            {{ Auth::guard('investor')->user()->name }}
                        </div>
                        <div class="font-medium text-sm text-gray-500">
                            {{ Auth::guard('investor')->user()->email }}
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <a href="{{ route('investor.profile') }}" class="block pl-3 pr-4 py-2 text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                            Profile
                        </a>
                        <form method="POST" action="{{ route('investor.logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left block pl-3 pr-4 py-2 text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        @if(isset($header))
            <header class="bg-gradient-to-br from-brand-accent-light/70 via-brand-light/40 to-gray-50 shadow-md">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {!! $header !!}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>
    
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggle = document.getElementById('darkModeToggle');
            const root = document.documentElement;

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