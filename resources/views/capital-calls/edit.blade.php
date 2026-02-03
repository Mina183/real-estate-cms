<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Capital Call') }} - {{ $capitalCall->call_number }}
            </h2>
            <a href="{{ route('capital-calls.show', $capitalCall) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Cancel
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('capital-calls.update', $capitalCall) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900">Capital Call Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Call Number (read-only) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Call Number</label>
                                <input type="text" value="{{ $capitalCall->call_number }}" disabled
                                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                            </div>

                            {{-- Status --}}
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                                <select name="status" id="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">
                                    <option value="draft" {{ old('status', $capitalCall->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="issued" {{ old('status', $capitalCall->status) === 'issued' ? 'selected' : '' }}>Issued</option>
                                    <option value="partially_paid" {{ old('status', $capitalCall->status) === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                                    <option value="fully_paid" {{ old('status', $capitalCall->status) === 'fully_paid' ? 'selected' : '' }}>Fully Paid</option>
                                    <option value="overdue" {{ old('status', $capitalCall->status) === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                </select>
                            </div>

                            {{-- Title --}}
                            <div class="col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                                <input type="text" name="title" id="title" value="{{ old('title', $capitalCall->title) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" id="description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">{{ old('description', $capitalCall->description) }}</textarea>
                            </div>

                            {{-- Total Amount --}}
                            <div>
                                <label for="total_amount" class="block text-sm font-medium text-gray-700">Total Amount *</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="total_amount" id="total_amount" step="0.01" min="0" 
                                        value="{{ old('total_amount', $capitalCall->total_amount) }}" required
                                        class="mt-1 block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">
                                </div>
                            </div>

                            {{-- Total Received (read-only) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Total Received</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="text" value="{{ number_format($capitalCall->total_received, 2) }}" disabled
                                        class="mt-1 block w-full pl-7 rounded-md border-gray-300 bg-gray-100 shadow-sm">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Automatically calculated from payments</p>
                            </div>

                            {{-- Call Date --}}
                            <div>
                                <label for="call_date" class="block text-sm font-medium text-gray-700">Call Date *</label>
                                <input type="date" name="call_date" id="call_date" 
                                    value="{{ old('call_date', $capitalCall->call_date->format('Y-m-d')) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">
                            </div>

                            {{-- Due Date --}}
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date *</label>
                                <input type="date" name="due_date" id="due_date" 
                                    value="{{ old('due_date', $capitalCall->due_date->format('Y-m-d')) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">
                            </div>

                            {{-- Notes --}}
                            <div class="col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700">Internal Notes</label>
                                <textarea name="notes" id="notes" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">{{ old('notes', $capitalCall->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Current Payments (read-only info) --}}
                @if($capitalCall->payments->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900">Existing Payments</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Payment amounts cannot be edited here. Use "Mark as Paid" from the capital call detail page.
                            </p>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Investor</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($capitalCall->payments as $payment)
                                            <tr>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $payment->investor->organization_name ?? $payment->investor->legal_entity_name }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                    ${{ number_format($payment->amount, 2) }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    @php
                                                        $statusColors = [
                                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                                            'paid' => 'bg-green-100 text-green-800',
                                                            'failed' => 'bg-red-100 text-red-800',
                                                        ];
                                                    @endphp
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$payment->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                        {{ ucfirst($payment->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Submit Buttons --}}
                <div class="flex justify-between">
                    <div>
                        @if($capitalCall->payments()->where('status', 'paid')->count() === 0)
                            <button type="button" onclick="deleteCapitalCall()" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-6 rounded">
                                Delete
                            </button>
                        @endif
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('capital-calls.show', $capitalCall) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded">
                            Cancel
                        </a>
                        <button type="submit" class="bg-brand-dark hover:bg-brand-darker text-white font-bold py-2 px-6 rounded">
                            Update Capital Call
                        </button>
                    </div>
                </div>
            </form>

            {{-- Delete Form --}}
            <form id="delete-form" action="{{ route('capital-calls.destroy', $capitalCall) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <script>
        function deleteCapitalCall() {
            if (confirm('Are you sure you want to delete this capital call? This action cannot be undone.')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
</x-app-layout>
