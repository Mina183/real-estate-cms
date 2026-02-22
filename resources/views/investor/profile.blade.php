@extends('layouts.investor-base')

@php
    $header = '<h2 class="font-semibold text-2xl text-brand-darker leading-tight">
            My Profile
        </h2>
        <p class="mt-1 text-sm text-brand-dark">
            Account and investment information
        </p>';
@endphp

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Account Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Account Information</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ $investorUser->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Account Status</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $investorUser->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800' }}">
                                {{ $investorUser->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Last Login</label>
                        <p class="mt-1 text-gray-900 dark:text-white">
                            {{ $investorUser->last_login_at ? $investorUser->last_login_at->format('M d, Y g:i A') : 'Never' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</label>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ $investorUser->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Investor Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Investor Information</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Organization Name</label>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ $investor->organization_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Investor Type</label>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $investor->investor_type)) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Jurisdiction</label>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ $investor->jurisdiction }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Current Stage</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                {{ ucfirst(str_replace('_', ' ', $investor->stage)) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Investment Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Investment Information</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Total Commitment</label>
                        <p class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $investor->currency }} {{ number_format($investor->final_commitment_amount ?? $investor->target_commitment_amount ?? 0, 0) }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Funded Amount</label>
                        <p class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ $investor->currency }} {{ number_format($investor->funded_amount ?? 0, 0) }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Fund</label>
                        <p class="mt-1 text-gray-900 dark:text-white">
                            {{ $investor->fund ? $investor->fund->name : 'Not assigned' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Data Room Access</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                {{ ucfirst($investor->data_room_access_level ?? 'none') }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
            <div class="flex items-start space-x-3">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-blue-800 dark:text-blue-300">
                    <p class="font-semibold mb-1">Need to update your information?</p>
                    <p>Please contact your relationship manager or email <a href="mailto:support@triton.com" class="underline hover:text-blue-900">support@triton.com</a> to request changes to your profile.</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection