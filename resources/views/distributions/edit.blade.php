<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Distribution') }} - {{ $distribution->distribution_number }}
            </h2>
            <a href="{{ route('distributions.show', $distribution) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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

            <form action="{{ route('distributions.update', $distribution) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900">Distribution Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Distribution Number (read-only) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Distribution Number</label>
                                <input type="text" value="{{ $distribution->distribution_number }}" disabled
                                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                            </div>

                            {{-- Status --}}
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                                <select name="status" id="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">
                                    <option value="draft" {{ old('status', $distribution->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="approved" {{ old('status', $distribution->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="processing" {{ old('status', $distribution->status) === 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="completed" {{ old('status', $distribution->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>

                            {{-- Title --}}
                            <div class="col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                                <input type="text" name="title" id="title" value="{{ old('title', $distribution->title) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" id="description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">{{ old('description', $distribution->description) }}</textarea>
                            </div>

                            {{-- Type --}}
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Distribution Type *</label>
                                <select name="type" id="type" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">
                                    <option value="dividend" {{ old('type', $distribution->type) === 'dividend' ? 'selected' : '' }}>Dividend</option>
                                    <option value="return_of_capital" {{ old('type', $distribution->type) === 'return_of_capital' ? 'selected' : '' }}>Return of Capital</option>
                                    <option value="profit_share" {{ old('type', $distribution->type) === 'profit_share' ? 'selected' : '' }}>Profit Share</option>
                                    <option value="other" {{ old('type', $distribution->type) === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            {{-- Total Amount --}}
                            <div>
                                <label for="total_amount" class="block text-sm font-medium text-gray-700">Total Amount *</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="total_amount" id="total_amount" step="0.01" min="0" 
                                        value="{{ old('total_amount', $distribution->total_amount) }}" required
                                        class="mt-1 block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">
                                </div>
                            </div>

                            {{-- Total Distributed (read-only) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Total Distributed</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="text" value="{{ number_format($distribution->total_distributed, 2) }}" disabled
                                        class="mt-1 block w-full pl-7 rounded-md border-gray-300 bg-gray-100 shadow-sm">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Automatically calculated from payments</p>
                            </div>

                            {{-- Record Date --}}
                            <div>
                                <label for="record_date" class="block text-sm font-medium text-gray-700">Record Date *</label>
                                <input type="date" name="record_date" id="record_date" 
                                    value="{{ old('record_date', $distribution->record_date->format('Y-m-d')) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">
                            </div>

                            {{-- Distribution Date --}}
                            <div>
                                <label for="distribution_date" class="block text-sm font-medium text-gray-700">Distribution Date *</label>
                                <input type="date" name="distribution_date" id="distribution_date" 
                                    value="{{ old('distribution_date', $distribution->distribution_date->format('Y-m-d')) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">
                            </div>

                            {{-- Notes --}}
                            <div class="col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700">Internal Notes</label>
                                <textarea name="notes" id="notes" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">{{ old('notes', $distribution->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Current Payments (read-only info) --}}
                @if($distribution->payments->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900">Existing Payments</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Payment amounts cannot be edited here. Use "Mark as Paid" from the distribution detail page.
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
                                        @foreach($distribution->payments as $payment)
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
                        @if($distribution->completedPayments()->count() === 0)
                            <button type="button" onclick="deleteDistribution()" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-6 rounded">
                                Delete
                            </button>
                        @endif
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('distributions.show', $distribution) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded">
                            Cancel
                        </a>
                        <button type="submit" class="bg-brand-dark hover:bg-brand-darker text-white font-bold py-2 px-6 rounded">
                            Update Distribution
                        </button>
                    </div>
                </div>
            </form>

            {{-- Delete Form --}}
            <form id="delete-form" action="{{ route('distributions.destroy', $distribution) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <script>
        function deleteDistribution() {
            if (confirm('Are you sure you want to delete this distribution? This action cannot be undone.')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
</x-app-layout>
