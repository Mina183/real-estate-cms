<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Distribution') }}
            </h2>
            <a href="{{ route('distributions.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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

            <form action="{{ route('distributions.store') }}" method="POST">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900">Distribution Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Title --}}
                            <div class="col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark"
                                    placeholder="e.g., Q4 2025 Profit Distribution">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" id="description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark"
                                    placeholder="Description of this distribution">{{ old('description') }}</textarea>
                            </div>

                            {{-- Type --}}
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Distribution Type *</label>
                                <select name="type" id="type" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">
                                    <option value="dividend" {{ old('type') === 'dividend' ? 'selected' : '' }}>Dividend</option>
                                    <option value="return_of_capital" {{ old('type') === 'return_of_capital' ? 'selected' : '' }}>Return of Capital</option>
                                    <option value="profit_share" {{ old('type') === 'profit_share' ? 'selected' : '' }}>Profit Share</option>
                                    <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            {{-- Status --}}
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                                <select name="status" id="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">
                                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="approved" {{ old('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                </select>
                                <p class="mt-1 text-sm text-gray-500">Draft requires approval before processing</p>
                            </div>

                            {{-- Total Amount --}}
                            <div>
                                <label for="total_amount" class="block text-sm font-medium text-gray-700">Total Amount *</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="total_amount" id="total_amount" step="0.01" min="0" value="{{ old('total_amount') }}" required
                                        class="mt-1 block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark"
                                        placeholder="0.00">
                                </div>
                                @error('total_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Record Date --}}
                            <div>
                                <label for="record_date" class="block text-sm font-medium text-gray-700">Record Date *</label>
                                <input type="date" name="record_date" id="record_date" value="{{ old('record_date', date('Y-m-d')) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">
                                <p class="mt-1 text-sm text-gray-500">Date to determine eligible investors</p>
                            </div>

                            {{-- Distribution Date --}}
                            <div>
                                <label for="distribution_date" class="block text-sm font-medium text-gray-700">Distribution Date *</label>
                                <input type="date" name="distribution_date" id="distribution_date" value="{{ old('distribution_date') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">
                            </div>

                            {{-- Notes --}}
                            <div class="col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700">Internal Notes</label>
                                <textarea name="notes" id="notes" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark"
                                    placeholder="Internal notes (not visible to investors)">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Investor Allocations --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900">Investor Allocations</h3>
                        
                        @if($investors->count() > 0)
                            <div class="mb-4">
                                <button type="button" onclick="selectAllInvestors()" class="text-sm text-brand-dark hover:underline">
                                    Select All
                                </button>
                                <span class="mx-2 text-gray-400">|</span>
                                <button type="button" onclick="deselectAllInvestors()" class="text-sm text-brand-dark hover:underline">
                                    Deselect All
                                </button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Select</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Investor</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Funded Amount</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Distribution Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($investors as $investor)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="checkbox" name="investor_ids[]" value="{{ $investor->id }}" 
                                                        class="investor-checkbox rounded border-gray-300 text-brand-dark focus:ring-brand-dark"
                                                        onchange="toggleAmountInput(this, {{ $investor->id }})">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $investor->organization_name ?? $investor->legal_entity_name }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                                    ${{ number_format($investor->funded_amount, 0) }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="relative rounded-md shadow-sm">
                                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                            <span class="text-gray-500 sm:text-sm">$</span>
                                                        </div>
                                                        <input type="number" name="investor_amounts[]" step="0.01" min="0"
                                                            id="amount_{{ $investor->id }}"
                                                            class="amount-input block w-full pl-7 pr-12 rounded-md border-gray-300 focus:border-brand-dark focus:ring-brand-dark"
                                                            placeholder="0.00" disabled>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                No active investors with funded amounts found. <a href="{{ route('investors.index') }}" class="text-brand-dark hover:underline">Manage investors</a>.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Submit Buttons --}}
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('distributions.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded">
                        Cancel
                    </a>
                    <button type="submit" class="bg-brand-dark hover:bg-brand-darker text-white font-bold py-2 px-6 rounded">
                        Create Distribution
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleAmountInput(checkbox, investorId) {
            const amountInput = document.getElementById('amount_' + investorId);
            amountInput.disabled = !checkbox.checked;
            if (!checkbox.checked) {
                amountInput.value = '';
            }
        }

        function selectAllInvestors() {
            document.querySelectorAll('.investor-checkbox').forEach(checkbox => {
                checkbox.checked = true;
                toggleAmountInput(checkbox, checkbox.value);
            });
        }

        function deselectAllInvestors() {
            document.querySelectorAll('.investor-checkbox').forEach(checkbox => {
                checkbox.checked = false;
                toggleAmountInput(checkbox, checkbox.value);
            });
        }
    </script>
</x-app-layout>
