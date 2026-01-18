<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Investor: {{ $investor->organization_name ?? $investor->legal_entity_name }}
            </h2>
            <a href="{{ route('investors.show', $investor) }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Cancel
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            <strong class="font-bold">There were some errors with your submission:</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('investors.update', $investor) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">

                            {{-- Basic Information Section --}}
                            <div class="border-b pb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="investor_type" class="block text-sm font-medium text-gray-700 mb-2">
                                            Investor Type <span class="text-red-500">*</span>
                                        </label>
                                        <select name="investor_type" id="investor_type" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="individual" {{ $investor->investor_type === 'individual' ? 'selected' : '' }}>Individual</option>
                                            <option value="corporate" {{ $investor->investor_type === 'corporate' ? 'selected' : '' }}>Corporate</option>
                                            <option value="family_office" {{ $investor->investor_type === 'family_office' ? 'selected' : '' }}>Family Office</option>
                                            <option value="spv" {{ $investor->investor_type === 'spv' ? 'selected' : '' }}>SPV</option>
                                            <option value="fund" {{ $investor->investor_type === 'fund' ? 'selected' : '' }}>Fund</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="organization_name" class="block text-sm font-medium text-gray-700 mb-2">
                                            Organization Name
                                        </label>
                                        <input type="text" name="organization_name" id="organization_name"
                                               value="{{ old('organization_name', $investor->organization_name) }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label for="legal_entity_name" class="block text-sm font-medium text-gray-700 mb-2">
                                            Legal Entity Name
                                        </label>
                                        <input type="text" name="legal_entity_name" id="legal_entity_name"
                                               value="{{ old('legal_entity_name', $investor->legal_entity_name) }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label for="jurisdiction" class="block text-sm font-medium text-gray-700 mb-2">
                                            Jurisdiction <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="jurisdiction" id="jurisdiction" required
                                               value="{{ old('jurisdiction', $investor->jurisdiction) }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>

                            {{-- Fund & Assignment --}}
                            <div class="border-b pb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Fund & Assignment</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="fund_id" class="block text-sm font-medium text-gray-700 mb-2">
                                            Fund
                                        </label>
                                        <select name="fund_id" id="fund_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">-- Select Fund --</option>
                                            @foreach($funds as $fund)
                                                <option value="{{ $fund->id }}" {{ $investor->fund_id == $fund->id ? 'selected' : '' }}>
                                                    {{ $fund->fund_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="assigned_to_user_id" class="block text-sm font-medium text-gray-700 mb-2">
                                            Assigned To
                                        </label>
                                        <select name="assigned_to_user_id" id="assigned_to_user_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">-- Select User --</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ $investor->assigned_to_user_id == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Financial Information --}}
                            <div class="border-b pb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Financial Information</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="target_commitment_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                            Target Commitment Amount
                                        </label>
                                        <input type="number" step="0.01" name="target_commitment_amount" id="target_commitment_amount"
                                               value="{{ old('target_commitment_amount', $investor->target_commitment_amount) }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                                            Currency
                                        </label>
                                        <select name="currency" id="currency"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="USD" {{ $investor->currency === 'USD' ? 'selected' : '' }}>USD</option>
                                            <option value="EUR" {{ $investor->currency === 'EUR' ? 'selected' : '' }}>EUR</option>
                                            <option value="GBP" {{ $investor->currency === 'GBP' ? 'selected' : '' }}>GBP</option>
                                            <option value="AED" {{ $investor->currency === 'AED' ? 'selected' : '' }}>AED</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- COMPLIANCE GATES SECTION --}}
                            <div class="border-b pb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">🔐 Compliance & Verification</h3>
                                
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                                    <p class="text-sm text-blue-800">
                                        <strong>Note:</strong> These compliance checkboxes control stage progression. Certain stages require specific verifications to be completed.
                                    </p>
                                </div>

                                <div class="space-y-4">
                                    {{-- Professional Client --}}
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="is_professional_client" id="is_professional_client"
                                                   value="1" {{ $investor->is_professional_client ? 'checked' : '' }}
                                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="is_professional_client" class="font-medium text-gray-700">
                                                Professional Client Status Confirmed
                                            </label>
                                            <p class="text-gray-500">Investor meets professional/qualified investor criteria</p>
                                            @if($investor->professional_client_verified_at)
                                                <p class="text-xs text-green-600 mt-1">
                                                    ✓ Verified on {{ $investor->professional_client_verified_at->format('M d, Y H:i') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Sanctions Check --}}
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="sanctions_check_passed" id="sanctions_check_passed"
                                                   value="1" {{ $investor->sanctions_check_passed ? 'checked' : '' }}
                                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="sanctions_check_passed" class="font-medium text-gray-700">
                                                Sanctions Check Passed
                                            </label>
                                            <p class="text-gray-500">Investor cleared sanctions and PEP screening</p>
                                            @if($investor->sanctions_checked_at)
                                                <p class="text-xs text-green-600 mt-1">
                                                    ✓ Checked on {{ $investor->sanctions_checked_at->format('M d, Y H:i') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Bank Verified --}}
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="bank_account_verified" id="bank_account_verified"
                                                   value="1" {{ $investor->bank_account_verified ? 'checked' : '' }}
                                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="bank_account_verified" class="font-medium text-gray-700">
                                                Bank Account Verified
                                            </label>
                                            <p class="text-gray-500">Bank account details confirmed and validated</p>
                                            @if($investor->bank_verified_at)
                                                <p class="text-xs text-green-600 mt-1">
                                                    ✓ Verified on {{ $investor->bank_verified_at->format('M d, Y H:i') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Confidentiality Acknowledgement --}}
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="confidentiality_acknowledged" id="confidentiality_acknowledged"
                                                   value="1" {{ $investor->confidentiality_acknowledged ? 'checked' : '' }}
                                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="confidentiality_acknowledged" class="font-medium text-gray-700">
                                                Confidentiality Agreement Acknowledged
                                            </label>
                                            <p class="text-gray-500">Investor has agreed to confidentiality terms</p>
                                            @if($investor->confidentiality_acknowledged_at)
                                                <p class="text-xs text-green-600 mt-1">
                                                    ✓ Acknowledged on {{ $investor->confidentiality_acknowledged_at->format('M d, Y H:i') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Source & Notes --}}
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="source_of_introduction" class="block text-sm font-medium text-gray-700 mb-2">
                                            Source of Introduction
                                        </label>
                                        <select name="source_of_introduction" id="source_of_introduction"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">-- Select Source --</option>
                                            <option value="direct" {{ $investor->source_of_introduction === 'direct' ? 'selected' : '' }}>Direct</option>
                                            <option value="advisor" {{ $investor->source_of_introduction === 'advisor' ? 'selected' : '' }}>Advisor</option>
                                            <option value="placement_agent" {{ $investor->source_of_introduction === 'placement_agent' ? 'selected' : '' }}>Placement Agent</option>
                                            <option value="referral" {{ $investor->source_of_introduction === 'referral' ? 'selected' : '' }}>Referral</option>
                                            <option value="event" {{ $investor->source_of_introduction === 'event' ? 'selected' : '' }}>Event</option>
                                            <option value="other" {{ $investor->source_of_introduction === 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>

                                    <div id="referral-source-field" style="display: {{ $investor->source_of_introduction === 'referral' ? 'block' : 'none' }};">
                                        <label for="referral_source" class="block text-sm font-medium text-gray-700 mb-2">
                                            Referral Source Details
                                        </label>
                                        <input type="text" name="referral_source" id="referral_source"
                                               value="{{ old('referral_source', $investor->referral_source) }}"
                                               placeholder="e.g., Name of person/company who referred"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                            Notes
                                        </label>
                                        <textarea name="notes" id="notes" rows="4"
                                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $investor->notes) }}</textarea>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-8 flex items-center justify-end space-x-3">
                            <a href="{{ route('investors.show', $investor) }}" 
                               class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded">
                                Update Investor
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle referral source field
        document.getElementById('source_of_introduction').addEventListener('change', function() {
            const referralField = document.getElementById('referral-source-field');
            referralField.style.display = this.value === 'referral' ? 'block' : 'none';
        });
    </script>

</x-app-layout>