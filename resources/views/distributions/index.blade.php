<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Distributions') }}
            </h2>
            <a href="{{ route('distributions.create') }}" class="bg-brand-dark hover:bg-brand-darker text-white font-bold py-2 px-4 rounded">
                + New Distribution
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Total Distributions</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_distributions'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Pending</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending_distributions'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Completed</div>
                    <div class="text-2xl font-bold text-green-600">{{ $stats['completed_distributions'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Total Distributed</div>
                    <div class="text-2xl font-bold text-brand-dark">${{ number_format($stats['total_amount_distributed'], 0) }}</div>
                </div>
            </div>

            {{-- Success Message --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Distributions Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Distribution #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Distributed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($distributions as $distribution)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $distribution->distribution_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $distribution->title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $distribution->type_label }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $distribution->distribution_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($distribution->total_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                        ${{ number_format($distribution->total_distributed, 2) }}
                                        <span class="text-xs text-gray-500">({{ number_format($distribution->distribution_percentage, 1) }}%)</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'draft' => 'bg-gray-100 text-gray-800',
                                                'approved' => 'bg-blue-100 text-blue-800',
                                                'processing' => 'bg-yellow-100 text-yellow-800',
                                                'completed' => 'bg-green-100 text-green-800',
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$distribution->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($distribution->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('distributions.show', $distribution) }}" class="text-brand-dark hover:text-brand-darker mr-3">View</a>
                                        <a href="{{ route('distributions.edit', $distribution) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                        @if($distribution->status === 'draft')
                                            <form action="{{ route('distributions.approve', $distribution) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Approve this distribution?')">Approve</button>
                                            </form>
                                        @elseif($distribution->status === 'approved')
                                            <form action="{{ route('distributions.process', $distribution) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-blue-600 hover:text-blue-900" onclick="return confirm('Start processing this distribution?')">Process</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                        No distributions found. <a href="{{ route('distributions.create') }}" class="text-brand-dark hover:underline">Create your first distribution</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $distributions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
