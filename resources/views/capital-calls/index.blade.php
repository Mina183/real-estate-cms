<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Capital Calls') }}
            </h2>
            <a href="{{ route('capital-calls.create') }}" class="bg-brand-dark hover:bg-brand-darker text-white font-bold py-2 px-4 rounded">
                + New Capital Call
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Total Calls</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_calls'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Active</div>
                    <div class="text-2xl font-bold text-brand-dark">{{ $stats['active_calls'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Overdue</div>
                    <div class="text-2xl font-bold text-red-600">{{ $stats['overdue_calls'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Total Called</div>
                    <div class="text-2xl font-bold text-gray-900">${{ number_format($stats['total_amount_called'], 0) }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Total Received</div>
                    <div class="text-2xl font-bold text-green-600">${{ number_format($stats['total_amount_received'], 0) }}</div>
                </div>
            </div>

            {{-- Success Message --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Capital Calls Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Call Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Call Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Received</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($capitalCalls as $call)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $call->call_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $call->title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $call->call_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $call->due_date->format('M d, Y') }}
                                        @if($call->isOverdue())
                                            <span class="text-red-600 font-semibold">(Overdue)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($call->total_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                        ${{ number_format($call->total_received, 2) }}
                                        <span class="text-xs text-gray-500">({{ number_format($call->payment_percentage, 1) }}%)</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'draft' => 'bg-gray-100 text-gray-800',
                                                'issued' => 'bg-blue-100 text-blue-800',
                                                'partially_paid' => 'bg-yellow-100 text-yellow-800',
                                                'fully_paid' => 'bg-green-100 text-green-800',
                                                'overdue' => 'bg-red-100 text-red-800',
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$call->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst(str_replace('_', ' ', $call->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('capital-calls.show', $call) }}" class="text-brand-dark hover:text-brand-darker mr-3">View</a>
                                        <a href="{{ route('capital-calls.edit', $call) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                        @if($call->status === 'draft')
                                            <form action="{{ route('capital-calls.issue', $call) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Issue this capital call?')">Issue</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                        No capital calls found. <a href="{{ route('capital-calls.create') }}" class="text-brand-dark hover:underline">Create your first capital call</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $capitalCalls->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
