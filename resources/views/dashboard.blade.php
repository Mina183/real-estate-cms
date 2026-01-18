<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-darker leading-tight">
            Investment Fund Dashboard
        </h2>
        <div class="text-sm text-brand-dark">
            Logged in as: {{ auth()->user()->name }} ({{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }})
        </div>
    </x-slot>

    <div class="min-h-screen bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex min-h-screen">
            
            {{-- ========================= SIDEBAR NAVIGATION ========================= --}}
            <aside class="w-72 bg-gradient-to-b from-brand-darker to-brand-dark text-white py-8 px-4 space-y-4 rounded-r-lg shadow-md">
                <h3 class="text-lg font-bold mb-4">Navigation</h3>
                <nav class="flex flex-col gap-2">
                    <a href="{{ route('dashboard') }}"
                       class="block bg-white text-brand-darker px-4 py-2 rounded hover:bg-brand-light/20 font-semibold transition">
                        📊 Dashboard
                    </a>
                    <a href="{{ route('investors.index') }}"
                       class="block bg-white text-brand-darker px-4 py-2 rounded hover:bg-brand-light/20 font-semibold transition">
                        👥 Investors
                    </a>
                    <a href="{{ route('data-room.index') }}"
                       class="block bg-white text-brand-darker px-4 py-2 rounded hover:bg-brand-light/20 font-semibold transition">
                        🔒 Data Room
                    </a>
                    <a href="#" class="block bg-gray-300 text-gray-500 px-4 py-2 rounded cursor-not-allowed font-semibold">
                        💰 Capital Calls <span class="text-xs">(Coming Soon)</span>
                    </a>
                    <a href="#" class="block bg-gray-300 text-gray-500 px-4 py-2 rounded cursor-not-allowed font-semibold">
                        📈 Reports <span class="text-xs">(Coming Soon)</span>
                    </a>
                </nav>
            </aside>

            {{-- ========================= MAIN DASHBOARD CONTENT ========================= --}}
            <main class="flex-1 py-6 px-8 bg-gradient-to-br from-brand-light/20 to-gray-100">
                <div class="max-w-6xl mx-auto">
                    <h3 class="text-2xl font-bold text-brand-darker mb-6">Welcome, {{ auth()->user()->name }}!</h3>

                    {{-- Stats Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        {{-- Total Investors --}}
                        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-brand-dark">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 uppercase">Total Investors</p>
                                    <p class="text-3xl font-bold text-brand-darker">{{ $stats['totalInvestors'] ?? 0 }}</p>
                                </div>
                                <div class="bg-brand-dark/10 rounded-full p-3">
                                    <svg class="w-8 h-8 text-brand-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Active Investors --}}
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 uppercase">Active Investors</p>
                                    <p class="text-3xl font-bold text-green-600">{{ $stats['activeInvestors'] ?? 0 }}</p>
                                </div>
                                <div class="bg-green-100 rounded-full p-3">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Prospects --}}
                        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-brand-accent">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 uppercase">Prospects</p>
                                    <p class="text-3xl font-bold text-brand-accent">{{ $stats['prospectInvestors'] ?? 0 }}</p>
                                </div>
                                <div class="bg-brand-accent/10 rounded-full p-3">
                                    <svg class="w-8 h-8 text-brand-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Active Stage --}}
                        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-brand-accent-light">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 uppercase">Funded Investors</p>
                                    <p class="text-3xl font-bold text-brand-dark">{{ $stats['activeStageInvestors'] ?? 0 }}</p>
                                </div>
                                <div class="bg-brand-dark/10 rounded-full p-3">
                                    <svg class="w-8 h-8 text-brand-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Investors --}}
                    <div class="bg-white rounded-lg shadow mb-6">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h4 class="text-lg font-semibold text-gray-900">Recent Investors</h4>
                                <a href="{{ route('investors.index') }}" class="text-brand-accent hover:text-brand-accent-light text-sm font-semibold">
                                    View All →
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            @if($recentInvestors->count() > 0)
                                <div class="space-y-4">
                                    @foreach($recentInvestors as $investor)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                            <div class="flex-1">
                                                <h5 class="font-semibold text-gray-900">
                                                    {{ $investor->organization_name ?? $investor->legal_entity_name }}
                                                </h5>
                                                <div class="flex items-center space-x-3 mt-1">
                                                    <span class="text-sm text-gray-500">{{ $investor->jurisdiction }}</span>
                                                    <span class="px-2 py-1 text-xs font-semibold rounded
                                                        @if($investor->stage === 'prospect') bg-gray-100 text-gray-800
                                                        @elseif($investor->stage === 'active') bg-green-100 text-green-800
                                                        @else bg-brand-dark/10 text-brand-dark
                                                        @endif">
                                                        {{ str_replace('_', ' ', ucfirst($investor->stage)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ $investor->currency }} {{ number_format($investor->target_commitment_amount ?? 0) }}
                                                </p>
                                                <a href="{{ route('investors.show', $investor) }}" 
                                                   class="text-sm text-brand-accent hover:text-brand-accent-light">
                                                    View Details →
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="mt-2">No investors yet</p>
                                    <a href="{{ route('investors.create') }}" 
                                       class="mt-4 inline-block bg-brand-accent hover:bg-brand-accent-light text-white font-bold py-2 px-4 rounded">
                                        + Add First Investor
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- My Investors (if Relationship Manager) --}}
                    @if($myInvestors->count() > 0)
                        <div class="bg-white rounded-lg shadow">
                            <div class="p-6 border-b border-gray-200">
                                <h4 class="text-lg font-semibold text-gray-900">My Assigned Investors</h4>
                            </div>
                            <div class="p-6">
                                <div class="space-y-3">
                                    @foreach($myInvestors as $investor)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded hover:bg-gray-100 transition">
                                            <div>
                                                <p class="font-semibold text-gray-900">
                                                    {{ $investor->organization_name ?? $investor->legal_entity_name }}
                                                </p>
                                                <p class="text-sm text-gray-500">{{ $investor->fund->fund_name ?? 'No fund assigned' }}</p>
                                            </div>
                                            <a href="{{ route('investors.show', $investor) }}" 
                                               class="text-brand-accent hover:text-brand-accent-light text-sm font-semibold">
                                                View →
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </main>

        </div>
    </div>
</x-app-layout>