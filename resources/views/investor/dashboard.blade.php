<x-layouts.investor-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-brand-darker leading-tight">
            Welcome, {{ $investor->organization_name ?? 'Investor' }}
        </h2>
        <p class="mt-1 text-sm text-brand-dark">
            Stage: <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $stats['stage'])) }}</span> | 
            Status: <span class="font-semibold">{{ ucfirst($stats['status']) }}</span>
        </p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Portfolio Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Commitment Amount -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 uppercase">Total Commitment</p>
                            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                {{ $stats['currency'] }} {{ number_format($stats['commitment'], 0) }}
                            </p>
                        </div>
                        <div class="bg-blue-100 dark:bg-blue-900/30 rounded-full p-3">
                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Funded Amount -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 uppercase">Funded Amount</p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                                {{ $stats['currency'] }} {{ number_format($stats['funded'], 0) }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $stats['funded_percentage'] }}% of commitment
                            </p>
                        </div>
                        <div class="bg-green-100 dark:bg-green-900/30 rounded-full p-3">
                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Distributions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 uppercase">Total Distributions</p>
                            <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                                {{ $stats['currency'] }} {{ number_format($totalDistributed, 0) }}
                            </p>
                        </div>
                        <div class="bg-purple-100 dark:bg-purple-900/30 rounded-full p-3">
                            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Capital Calls -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Capital Calls</h3>
                    </div>
                    <div class="p-6">
                        @if($capitalCalls->count() > 0)
                            <div class="space-y-4">
                                @foreach($capitalCalls as $call)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ $call->reference_number }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Due: {{ $call->due_date->format('M d, Y') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-gray-900 dark:text-white">{{ $call->currency }} {{ number_format($call->amount, 0) }}</p>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($call->status === 'issued') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                                @elseif($call->status === 'paid') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                                @elseif($call->status === 'overdue') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                                @endif">
                                                {{ ucfirst($call->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($pendingCapitalCalls > 0)
                                <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                    <p class="text-sm font-medium text-yellow-800 dark:text-yellow-400">
                                        Outstanding Amount: {{ $stats['currency'] }} {{ number_format($pendingCapitalCalls, 0) }}
                                    </p>
                                </div>
                            @endif
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-center py-8">No capital calls yet</p>
                        @endif
                    </div>
                </div>

                <!-- Distributions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Distributions</h3>
                    </div>
                    <div class="p-6">
                        @if($distributions->count() > 0)
                            <div class="space-y-4">
                                @foreach($distributions as $dist)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ $dist->reference_number }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $dist->distribution_date->format('M d, Y') }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $dist->distribution_type)) }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-green-600 dark:text-green-400">{{ $dist->currency }} {{ number_format($dist->amount, 0) }}</p>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($dist->status === 'draft') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                                @elseif($dist->status === 'approved') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                                @elseif($dist->status === 'processed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                                @endif">
                                                {{ ucfirst($dist->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-center py-8">No distributions yet</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('investor.documents') }}" class="flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        View Documents
                    </a>
                    <a href="{{ route('investor.profile') }}" class="flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        My Profile
                    </a>
                    <a href="mailto:support@triton.com" class="flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Contact Support
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-investor-layout>