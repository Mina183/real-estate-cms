<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $capitalCall->call_number }} - {{ $capitalCall->title }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('capital-calls.edit', $capitalCall) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                <a href="{{ route('capital-calls.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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

            {{-- Capital Call Details --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Capital Call Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Call Number</label>
                                <div class="mt-1 text-gray-900">{{ $capitalCall->call_number }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Title</label>
                                <div class="mt-1 text-gray-900">{{ $capitalCall->title }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Call Date</label>
                                <div class="mt-1 text-gray-900">{{ $capitalCall->call_date->format('F d, Y') }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Due Date</label>
                                <div class="mt-1 text-gray-900">
                                    {{ $capitalCall->due_date->format('F d, Y') }}
                                    @if($capitalCall->isOverdue())
                                        <span class="text-red-600 font-semibold ml-2">(Overdue)</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <div class="mt-1">
                                    @php
                                        $statusColors = [
                                            'draft' => 'bg-gray-100 text-gray-800',
                                            'issued' => 'bg-blue-100 text-blue-800',
                                            'partially_paid' => 'bg-yellow-100 text-yellow-800',
                                            'fully_paid' => 'bg-green-100 text-green-800',
                                            'overdue' => 'bg-red-100 text-red-800',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusColors[$capitalCall->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst(str_replace('_', ' ', $capitalCall->status)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                                <div class="mt-1 text-2xl font-bold text-gray-900">${{ number_format($capitalCall->total_amount, 2) }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Amount Received</label>
                                <div class="mt-1 text-2xl font-bold text-green-600">${{ number_format($capitalCall->total_received, 2) }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Outstanding</label>
                                <div class="mt-1 text-2xl font-bold text-red-600">${{ number_format($capitalCall->outstanding_amount, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    @if($capitalCall->description)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <div class="mt-1 text-gray-900">{{ $capitalCall->description }}</div>
                        </div>
                    @endif

                    @if($capitalCall->notes)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700">Notes</label>
                            <div class="mt-1 text-gray-900">{{ $capitalCall->notes }}</div>
                        </div>
                    @endif

                    {{-- Progress Bar --}}
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Progress</label>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-green-600 h-4 rounded-full" style="width: {{ $capitalCall->payment_percentage }}%"></div>
                        </div>
                        <div class="text-sm text-gray-600 mt-1">{{ number_format($capitalCall->payment_percentage, 1) }}% collected</div>
                    </div>

                    {{-- Action Buttons --}}
                    @if($capitalCall->status === 'draft')
                        <div class="mt-6">
                            <form action="{{ route('capital-calls.issue', $capitalCall) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Issue this capital call to all investors?')">
                                    Issue Capital Call
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Payment Transactions --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Investor Payments ({{ $stats['total_payments'] }})</h3>
                    
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
                            @forelse ($capitalCall->payments as $payment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <a href="{{ route('investors.show', $payment->investor) }}" class="text-brand-dark hover:underline">
                                            {{ $payment->investor->organization_name ?? $payment->investor->legal_entity_name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        ${{ number_format($payment->amount, 2) }}
                                        @if($payment->commitment_percentage)
                                            <span class="text-xs text-gray-500">({{ number_format($payment->commitment_percentage, 1) }}%)</span>
                                        @endif
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
                                        @if($payment->isOverdue())
                                            <span class="text-red-600 font-semibold">(Overdue)</span>
                                        @endif
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
                                        No payment transactions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Mark as Paid Modal (placeholder - would need Alpine.js or similar) --}}
    <script>
        function openMarkPaidModal(paymentId) {
            // TODO: Implement modal for marking payment as paid
            if(confirm('Mark this payment as paid?')) {
                // Submit form
                alert('Modal implementation needed');
            }
        }

        function openReverseModal(paymentId) {
            if(confirm('Are you sure you want to reverse this payment?')) {
                // Submit reverse form
                alert('Modal implementation needed');
            }
        }
    </script>
</x-app-layout>
