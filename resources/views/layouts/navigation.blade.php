<nav x-data="{ open: false }" class="bg-brand-darker border-b border-brand-dark dark:border-brand-dark">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 min-h-[80px] py-4">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="/images/logo.png" alt="Logo" class="h-20 w-auto">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex items-center text-white">

                    {{-- Dashboard with hover sub-menu (Investors, Capital Calls, Distributions) --}}
                    <div x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" class="relative flex items-center">
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center text-sm font-medium focus:outline-none transition
                               {{ request()->routeIs('dashboard') || request()->routeIs('investors.*') || request()->routeIs('capital-calls.*') || request()->routeIs('distributions.*')
                                   ? 'text-brand-accent'
                                   : 'text-white hover:text-brand-accent' }}">
                            {{ __('Dashboard') }}
                            <svg class="ml-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <div x-show="show"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute top-full left-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 py-1 ring-1 ring-black ring-opacity-5"
                             style="display: none;">
                            @auth
                                @can('viewAny', App\Models\Investor::class)
                                    <a href="{{ route('investors.index') }}"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('investors.*') ? 'font-semibold bg-gray-50' : '' }}">
                                        {{ __('Investors') }}
                                    </a>
                                @endcan
                                @can('manage-capital-calls')
                                    <a href="{{ route('capital-calls.index') }}"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('capital-calls.*') ? 'font-semibold bg-gray-50' : '' }}">
                                        {{ __('Capital Calls') }}
                                    </a>
                                @endcan
                                @can('manage-distributions')
                                    <a href="{{ route('distributions.index') }}"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('distributions.*') ? 'font-semibold bg-gray-50' : '' }}">
                                        {{ __('Distributions') }}
                                    </a>
                                @endcan
                            @endauth
                        </div>
                    </div>

                    @auth
                        <x-nav-link :href="route('data-room.index')" :active="request()->routeIs('data-room.*')" class="text-white hover:text-brand-accent">
                            {{ __('Data Room') }}
                        </x-nav-link>

                        @can('manage-access-requests')
                            <x-nav-link :href="route('document-access-requests.index')" :active="request()->routeIs('document-access-requests.*')" class="text-white hover:text-brand-accent">
                                {{ __('Access Requests') }}
                            </x-nav-link>
                        @endcan

                        @can('manage-settings')
                            <x-dropdown align="left" width="52">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center text-sm font-medium text-white hover:text-brand-accent focus:outline-none transition">
                                        {{ __('Settings') }}
                                        <svg class="ml-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('email-drafts.index')" :active="request()->routeIs('email-drafts.*')">
                                        {{ __('Email Draft Approvals') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('email-body-templates.index')" :active="request()->routeIs('email-body-templates.*')">
                                        {{ __('Email Templates') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('document-packages.index')" :active="request()->routeIs('document-packages.*')">
                                        {{ __('Document Packages') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('data-room-viewers.index')" :active="request()->routeIs('data-room-viewers.*')">
                                        {{ __('Data Room Viewers') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        @endcan
                    @endauth
                </div>
            </div>

            <!-- User Dropdown (top right) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            @auth
                                <div>{{ Auth::user()->name }}</div>
                            @else
                                <div>Guest</div>
                            @endauth
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-brand-light hover:text-white hover:bg-brand-dark/20 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-brand-dark/30 shadow-lg">
        <div class="pt-2 pb-3 space-y-1">

            {{-- Dashboard with collapsible sub-items --}}
            <div x-data="{ showSub: {{ request()->routeIs('dashboard') || request()->routeIs('investors.*') || request()->routeIs('capital-calls.*') || request()->routeIs('distributions.*') ? 'true' : 'false' }} }">
                <div class="flex items-center">
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="flex-1">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    @auth
                        <button @click="showSub = !showSub" class="px-4 py-2 text-gray-600 dark:text-gray-400">
                            <svg :class="{'rotate-180': showSub}" class="h-4 w-4 fill-current transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    @endauth
                </div>
                @auth
                    <div x-show="showSub" class="pl-4 border-l-2 border-gray-200 ml-4" style="display: none;">
                        @can('viewAny', App\Models\Investor::class)
                            <x-responsive-nav-link :href="route('investors.index')" :active="request()->routeIs('investors.*')">
                                {{ __('Investors') }}
                            </x-responsive-nav-link>
                        @endcan
                        @can('manage-capital-calls')
                            <x-responsive-nav-link :href="route('capital-calls.index')" :active="request()->routeIs('capital-calls.*')">
                                {{ __('Capital Calls') }}
                            </x-responsive-nav-link>
                        @endcan
                        @can('manage-distributions')
                            <x-responsive-nav-link :href="route('distributions.index')" :active="request()->routeIs('distributions.*')">
                                {{ __('Distributions') }}
                            </x-responsive-nav-link>
                        @endcan
                    </div>
                @endauth
            </div>

            @auth
                <x-responsive-nav-link :href="route('data-room.index')" :active="request()->routeIs('data-room.*')">
                    {{ __('Data Room') }}
                </x-responsive-nav-link>

                @can('manage-access-requests')
                    <x-responsive-nav-link :href="route('document-access-requests.index')" :active="request()->routeIs('document-access-requests.*')">
                        {{ __('Document Access Requests') }}
                    </x-responsive-nav-link>
                @endcan

                @can('manage-settings')
                    <div class="px-4 pt-3 pb-1">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Settings</span>
                    </div>
                    <x-responsive-nav-link :href="route('email-drafts.index')" :active="request()->routeIs('email-drafts.*')">
                        {{ __('Email Draft Approvals') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('email-body-templates.index')" :active="request()->routeIs('email-body-templates.*')">
                        {{ __('Email Templates') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('document-packages.index')" :active="request()->routeIs('document-packages.*')">
                        {{ __('Document Packages') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('data-room-viewers.index')" :active="request()->routeIs('data-room-viewers.*')">
                        {{ __('Data Room Viewers') }}
                    </x-responsive-nav-link>
                @endcan
            @endauth
        </div>

        <!-- Responsive User Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                @auth
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                @endauth
                @guest
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">Guest</div>
                    <div class="font-medium text-sm text-gray-500">Not logged in</div>
                @endguest
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
