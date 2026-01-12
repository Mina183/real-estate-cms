<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Investor') }}
            </h2>
            <a href="{{ route('investors.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <strong>Please fix the following errors:</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('investors.store') }}" method="POST">
                        @csrf

                        <div class="space-y-6">
                            
                            <!-- Investor Type -->
                            <div>
                                <label for="investor_type" class="block text-sm font-medium text-gray-700">
                                    Investor Type <span class="text-red-500">*</span>
                                </label>
                                <select name="investor_type" id="investor_type" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Type</option>
                                    <option value="individual" {{ old('investor_type') === 'individual' ? 'selected' : '' }}>Individual</option>
                                    <option value="corporate" {{ old('investor_type') === 'corporate' ? 'selected' : '' }}>Corporate</option>
                                    <option value="family_office" {{ old('investor_type') === 'family_office' ? 'selected' : '' }}>Family Office</option>
                                    <option value="spv" {{ old('investor_type') === 'spv' ? 'selected' : '' }}>SPV</option>
                                    <option value="fund" {{ old('investor_type') === 'fund' ? 'selected' : '' }}>Fund</option>
                                </select>
                            </div>

                            <!-- Organization Name -->
                            <div>
                                <label for="organization_name" class="block text-sm font-medium text-gray-700">
                                    Organization Name
                                </label>
                                <input type="text" name="organization_name" id="organization_name" 
                                       value="{{ old('organization_name') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-sm text-gray-500">For corporate entities, family offices, SPVs, and funds</p>
                            </div>

                            <!-- Legal Entity Name -->
                            <div>
                                <label for="legal_entity_name" class="block text-sm font-medium text-gray-700">
                                    Legal Entity Name
                                </label>
                                <input type="text" name="legal_entity_name" id="legal_entity_name" 
                                       value="{{ old('legal_entity_name') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-sm text-gray-500">Full legal name as registered</p>
                            </div>

                            <!-- Jurisdiction -->
                            <div>
                                <label for="jurisdiction" class="block text-sm font-medium text-gray-700">
                                    Jurisdiction <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="jurisdiction" id="jurisdiction" required
                                       value="{{ old('jurisdiction') }}"
                                       placeholder="e.g., UAE, Cayman Islands, Singapore"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <!-- Fund Selection -->
                            <div>
                                <label for="fund_id" class="block text-sm font-medium text-gray-700">
                                    Fund
                                </label>
                                <select name="fund_id" id="fund_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Fund (Optional)</option>
                                    @foreach($funds as $fund)
                                        <option value="{{ $fund->id }}" {{ old('fund_id') == $fund->id ? 'selected' : '' }}>
                                            {{ $fund->fund_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Target Commitment Amount -->
                            <div>
                                <label for="target_commitment_amount" class="block text-sm font-medium text-gray-700">
                                    Target Commitment Amount
                                </label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <select name="currency" 
                                            class="rounded-l-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                        <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD</option>
                                        <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR</option>
                                        <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>GBP</option>
                                        <option value="AED" {{ old('currency') === 'AED' ? 'selected' : '' }}>AED</option>
                                    </select>
                                    <input type="number" name="target_commitment_amount" id="target_commitment_amount" 
                                           value="{{ old('target_commitment_amount') }}"
                                           step="0.01" min="0"
                                           placeholder="1000000.00"
                                           class="flex-1 rounded-r-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>

                            <!-- Assigned To -->
                            <div>
                                <label for="assigned_to_user_id" class="block text-sm font-medium text-gray-700">
                                    Assign To
                                </label>
                                <select name="assigned_to_user_id" id="assigned_to_user_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Unassigned</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_to_user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ ucfirst(str_replace('_', ' ', $user->role)) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Source of Introduction -->
                            <div>
                                <label for="source_of_introduction" class="block text-sm font-medium text-gray-700">
                                    Source of Introduction
                                </label>
                                <select name="source_of_introduction" id="source_of_introduction"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Source</option>
                                    <option value="direct" {{ old('source_of_introduction') === 'direct' ? 'selected' : '' }}>Direct</option>
                                    <option value="advisor" {{ old('source_of_introduction') === 'advisor' ? 'selected' : '' }}>Advisor</option>
                                    <option value="placement_agent" {{ old('source_of_introduction') === 'placement_agent' ? 'selected' : '' }}>Placement Agent</option>
                                    <option value="referral" {{ old('source_of_introduction') === 'referral' ? 'selected' : '' }}>Referral</option>
                                    <option value="event" {{ old('source_of_introduction') === 'event' ? 'selected' : '' }}>Event</option>
                                    <option value="other" {{ old('source_of_introduction') === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <!-- Referral Source (if referral) -->
                            <div id="referral_source_div" class="hidden">
                                <label for="referral_source" class="block text-sm font-medium text-gray-700">
                                    Referral Source Details
                                </label>
                                <input type="text" name="referral_source" id="referral_source" 
                                       value="{{ old('referral_source') }}"
                                       placeholder="Name or organization that referred"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">
                                    Notes
                                </label>
                                <textarea name="notes" id="notes" rows="4"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                            </div>

                        </div>

                        <!-- Submit Buttons -->
                        <div class="mt-6 flex items-center justify-end space-x-3">
                            <a href="{{ route('investors.index') }}" 
                               class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Investor
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        // Show/hide referral source field based on selection
        document.getElementById('source_of_introduction').addEventListener('change', function() {
            const referralDiv = document.getElementById('referral_source_div');
            if (this.value === 'referral') {
                referralDiv.classList.remove('hidden');
            } else {
                referralDiv.classList.add('hidden');
            }
        });

        // Trigger on page load if old value is referral
        if (document.getElementById('source_of_introduction').value === 'referral') {
            document.getElementById('referral_source_div').classList.remove('hidden');
        }
    </script>

</x-app-layout>