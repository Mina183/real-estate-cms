<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $distribution->distribution_number }} - {{ $distribution->title }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('distributions.edit', $distribution) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                <a href="{{ route('distributions.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Messages --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Distribution Details --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Distribution Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Distribution Number</label>
                                <div class="mt-1 text-gray-900">{{ $distribution->distribution_number }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Title</label>
                                <div class="mt-1 text-gray-900">{{ $distribution->title }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Type</label>
                                <div class="mt-1 text-gray-900">{{ $distribution->type_label }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Record Date</label>
                                <div class="mt-1 text-gray-900">{{ $distribution->record_date->format('F d, Y') }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Distribution Date</label>
                                <div class="mt-1 text-gray-900">{{ $distribution->distribution_date->format('F d, Y') }}</div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <div class="mt-1">
                                    @php
                                        $statusColors = [
                                            'draft' => 'bg-gray-100 text-gray-800',
                                            'approved' => 'bg-blue-100 text-blue-800',
                                            'processing' => 'bg-yellow-100 text-yellow-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusColors[$distribution->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($distribution->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                                <div class="mt-1 text-2xl font-bold text-gray-900">${{ number_format($distribution->total_amount, 2) }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Amount Distributed</label>
                                <div class="mt-1 text-2xl font-bold text-green-600">${{ number_format($distribution->total_distributed, 2) }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Remaining</label>
                                <div class="mt-1 text-2xl font-bold text-orange-600">${{ number_format($distribution->remaining_amount, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    @if($distribution->description)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <div class="mt-1 text-gray-900">{{ $distribution->description }}</div>
                        </div>
                    @endif

                    @if($distribution->notes)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700">Notes</label>
                            <div class="mt-1 text-gray-900">{{ $distribution->notes }}</div>
                        </div>
                    @endif

                    {{-- Progress Bar --}}
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Distribution Progress</label>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-green-600 h-4 rounded-full" style="width: {{ $distribution->distribution_percentage }}%"></div>
                        </div>
                        <div class="text-sm text-gray-600 mt-1">{{ number_format($distribution->distribution_percentage, 1) }}% distributed</div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-6 flex space-x-3">
                        @if($distribution->status === 'draft')
                            <form action="{{ route('distributions.approve', $distribution) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Approve this distribution?')">
                                    Approve Distribution
                                </button>
                            </form>
                        @elseif($distribution->status === 'approved')
                            <form action="{{ route('distributions.process', $distribution) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Start processing payments?')">
                                    Start Processing
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Payment Transactions --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Investor Distributions ({{ $stats['total_payments'] }})</h3>
                    
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Investor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paid Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment Method</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($distribution->payments as $payment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <a href="{{ route('investors.show', $payment->investor) }}" class="text-brand-dark hover:underline">
                                            {{ $payment->investor->organization_name ?? $payment->investor->legal_entity_name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        ${{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $paymentStatusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'paid' => 'bg-green-100 text-green-800',
                                                'failed' => 'bg-red-100 text-red-800',
                                                'reversed' => 'bg-gray-100 text-gray-800',
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $paymentStatusColors[$payment->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $payment->due_date ? $payment->due_date->format('M d, Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $payment->paid_date ? $payment->paid_date->format('M d, Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $payment->payment_method_label }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($payment->status === 'pending')
                                            <button onclick="openMarkPaidModal({{ $payment->id }})" class="text-green-600 hover:text-green-900 mr-2">
                                                Mark Paid
                                            </button>
                                        @endif
                                        @if($payment->status === 'paid')
                                            <button onclick="openReverseModal({{ $payment->id }})" class="text-red-600 hover:text-red-900">
                                                Reverse
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No distribution transactions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Placeholder for modals --}}
    <script>
        function openMarkPaidModal(paymentId) {
            if(confirm('Mark this payment as paid?')) {
                alert('Modal implementation needed');
            }
        }

        function openReverseModal(paymentId) {
            if(confirm('Are you sure you want to reverse this payment?')) {
                alert('Modal implementation needed');
            }
        }
    </script>
</x-app-layout>
