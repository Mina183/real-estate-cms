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
                                            <input type="checkbox" name="agreed_confidentiality" id="agreed_confidentiality"
                                                value="1" {{ $investor->agreed_confidentiality ? 'checked' : '' }}
                                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="agreed_confidentiality" class="font-medium text-gray-700">
                                                Confidentiality Agreement Acknowledged
                                            </label>
                                            <p class="text-gray-500">Investor has agreed to confidentiality terms</p>
                                            @if($investor->agreed_confidentiality_at)
                                                <p class="text-xs text-green-600 mt-1">
                                                    ✓ Acknowledged on {{ $investor->agreed_confidentiality_at->format('M d, Y H:i') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Additional Compliance Fields --}}
                            <div class="border-b pb-6 mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-2 border-b-2 border-gray-200">📋 Workflow Progress Fields</h3>
                                
                                <div class="space-y-6">
                                    {{-- PPM Acknowledged --}}
                                    <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="ppm_acknowledged" id="ppm_acknowledged"
                                                   value="1" {{ $investor->ppm_acknowledged_date ? 'checked' : '' }}
                                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="ppm_acknowledged" class="font-medium text-gray-700">
                                                PPM Acknowledged
                                            </label>
                                            <p class="text-gray-500">Investor has acknowledged PPM and confidentiality terms</p>
                                            @if($investor->ppm_acknowledged_date)
                                                <p class="text-xs text-green-600 mt-1">
                                                    ✓ Acknowledged on {{ $investor->ppm_acknowledged_date->format('M d, Y H:i') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- KYC Status --}}
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <label for="kyc_status" class="block text-sm font-medium text-gray-700 mb-2">
                                            KYC Status
                                        </label>
                                        <select name="kyc_status" id="kyc_status"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">-- Not Started --</option>
                                            <option value="in_progress" {{ $investor->kyc_status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="submitted" {{ $investor->kyc_status === 'submitted' ? 'selected' : '' }}>Submitted</option>
                                            <option value="under_review" {{ $investor->kyc_status === 'under_review' ? 'selected' : '' }}>Under Review</option>
                                            <option value="complete" {{ $investor->kyc_status === 'complete' ? 'selected' : '' }}>Complete</option>
                                            <option value="rejected" {{ $investor->kyc_status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                        <p class="mt-1 text-sm text-gray-500">Current KYC verification status</p>
                                    </div>

                                    {{-- Subscription Signed --}}
                                    <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="subscription_signed" id="subscription_signed"
                                                   value="1" {{ $investor->subscription_signed_date ? 'checked' : '' }}
                                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="subscription_signed" class="font-medium text-gray-700">
                                                Subscription Agreement Signed
                                            </label>
                                            <p class="text-gray-500">Investor has signed subscription agreement</p>
                                            @if($investor->subscription_signed_date)
                                                <p class="text-xs text-green-600 mt-1">
                                                    ✓ Signed on {{ $investor->subscription_signed_date->format('M d, Y H:i') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Financial Fields --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-blue-50 rounded-lg border-t-4 border-blue-300">
                                        <div>
                                            <label for="final_commitment_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                                Final Commitment Amount
                                            </label>
                                            <input type="number" step="0.01" name="final_commitment_amount" id="final_commitment_amount"
                                                   value="{{ old('final_commitment_amount', $investor->final_commitment_amount) }}"
                                                   placeholder="Final committed amount"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <p class="mt-1 text-sm text-gray-500">Actual committed amount after subscription</p>
                                        </div>

                                        <div>
                                            <label for="funded_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                                Funded Amount
                                            </label>
                                            <input type="number" step="0.01" name="funded_amount" id="funded_amount"
                                                   value="{{ old('funded_amount', $investor->funded_amount) }}"
                                                   placeholder="Amount actually received"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <p class="mt-1 text-sm text-gray-500">Total amount funded to date</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- NEW FIELDS - Stage 2: Eligibility & Risk --}}
                            <div class="border-b pb-6 mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-2 border-b-2 border-gray-200">🎯 Stage 2: Eligibility & Risk Assessment</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Risk Profile --}}
                                    <div>
                                        <label for="risk_profile" class="block text-sm font-medium text-gray-700 mb-2">
                                            Risk Profile
                                        </label>
                                        <select name="risk_profile" id="risk_profile"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">-- Select Risk Profile --</option>
                                            <option value="low" {{ $investor->risk_profile === 'low' ? 'selected' : '' }}>Low</option>
                                            <option value="medium" {{ $investor->risk_profile === 'medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="high" {{ $investor->risk_profile === 'high' ? 'selected' : '' }}>High</option>
                                        </select>
                                        <p class="mt-1 text-sm text-gray-500">Investor risk classification</p>
                                    </div>

                                    {{-- Investor Experience --}}
                                    <div>
                                        <label for="investor_experience" class="block text-sm font-medium text-gray-700 mb-2">
                                            Investor Experience
                                        </label>
                                        <textarea name="investor_experience" id="investor_experience" rows="3"
                                                placeholder="Describe investor's experience with alternative investments..."
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('investor_experience', $investor->investor_experience) }}</textarea>
                                        <p class="mt-1 text-sm text-gray-500">Self-certified investment experience</p>
                                    </div>
                                </div>
                            </div>

                            {{-- NEW FIELDS - Stage 4: KYC/AML --}}
                            <div class="border-b pb-6 mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-2 border-b-2 border-gray-200">🔍 Stage 4: KYC/AML Details</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- KYC Risk Rating --}}
                                    <div>
                                        <label for="kyc_risk_rating" class="block text-sm font-medium text-gray-700 mb-2">
                                            KYC Risk Rating
                                        </label>
                                        <select name="kyc_risk_rating" id="kyc_risk_rating"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">-- Not Rated --</option>
                                            <option value="low" {{ $investor->kyc_risk_rating === 'low' ? 'selected' : '' }}>Low</option>
                                            <option value="medium" {{ $investor->kyc_risk_rating === 'medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="high" {{ $investor->kyc_risk_rating === 'high' ? 'selected' : '' }}>High</option>
                                        </select>
                                        <p class="mt-1 text-sm text-gray-500">KYC/AML risk assessment</p>
                                    </div>

                                    {{-- EDD Required --}}
                                    <div class="flex items-start p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="enhanced_due_diligence_required" id="enhanced_due_diligence_required"
                                                value="1" {{ $investor->enhanced_due_diligence_required ? 'checked' : '' }}
                                                class="focus:ring-yellow-500 h-4 w-4 text-yellow-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="enhanced_due_diligence_required" class="font-medium text-gray-700">
                                                Enhanced Due Diligence Required
                                            </label>
                                            <p class="text-gray-500">High-risk investor requires additional verification</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- NEW FIELDS - Stage 5: Subscription & Legal --}}
                            <div class="border-b pb-6 mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-2 border-b-2 border-gray-200">📄 Stage 5: Subscription & Legal Review</h3>
                                
                                <div class="space-y-4">
                                    {{-- Side Letter --}}
                                    <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="side_letter_exists" id="side_letter_exists"
                                                value="1" {{ $investor->side_letter_exists ? 'checked' : '' }}
                                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                                onchange="document.getElementById('side_letter_terms_field').style.display = this.checked ? 'block' : 'none';">
                                        </div>
                                        <div class="ml-3 text-sm flex-1">
                                            <label for="side_letter_exists" class="font-medium text-gray-700">
                                                Side Letter Exists
                                            </label>
                                            <p class="text-gray-500">Investor has negotiated side letter with special terms</p>
                                            
                                            <div id="side_letter_terms_field" style="display: {{ $investor->side_letter_exists ? 'block' : 'none' }};" class="mt-3">
                                                <label for="side_letter_terms" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Side Letter Key Terms
                                                </label>
                                                <textarea name="side_letter_terms" id="side_letter_terms" rows="3"
                                                        placeholder="Summarize key terms (fees, reporting, governance rights, etc.)"
                                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('side_letter_terms', $investor->side_letter_terms) }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Legal Review Complete --}}
                                    <div class="flex items-start p-4 bg-green-50 rounded-lg border border-green-200">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="legal_review_complete" id="legal_review_complete"
                                                value="1" {{ $investor->legal_review_complete ? 'checked' : '' }}
                                                class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="legal_review_complete" class="font-medium text-gray-700">
                                                Legal Review Complete
                                            </label>
                                            <p class="text-gray-500">All documents reviewed and approved by legal team</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- NEW FIELDS - Stage 6: Approval & Governance --}}
                            <div class="border-b pb-6 mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-2 border-b-2 border-gray-200">✅ Stage 6: Approval & Governance</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Board Approval Required --}}
                                    <div class="flex items-start p-4 bg-purple-50 rounded-lg border border-purple-200">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="board_approval_required" id="board_approval_required"
                                                value="1" {{ $investor->board_approval_required ? 'checked' : '' }}
                                                class="focus:ring-purple-500 h-4 w-4 text-purple-600 border-gray-300 rounded"
                                                onchange="document.getElementById('board_approval_date_field').style.display = this.checked ? 'block' : 'none';">
                                        </div>
                                        <div class="ml-3 text-sm flex-1">
                                            <label for="board_approval_required" class="font-medium text-gray-700">
                                                Board Approval Required
                                            </label>
                                            <p class="text-gray-500">Large commitment requires board/IC approval</p>
                                            
                                            <div id="board_approval_date_field" style="display: {{ $investor->board_approval_required ? 'block' : 'none' }};" class="mt-3">
                                                <label for="board_approval_date" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Board Approval Date
                                                </label>
                                                <input type="date" name="board_approval_date" id="board_approval_date"
                                                    value="{{ old('board_approval_date', $investor->board_approval_date?->format('Y-m-d')) }}"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Admission Notice --}}
                                    <div>
                                        <label for="admission_notice_issued_date" class="block text-sm font-medium text-gray-700 mb-2">
                                            Admission Notice Issued Date
                                        </label>
                                        <input type="date" name="admission_notice_issued_date" id="admission_notice_issued_date"
                                            value="{{ old('admission_notice_issued_date', $investor->admission_notice_issued_date?->format('Y-m-d')) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <p class="mt-1 text-sm text-gray-500">Date investor formally admitted to fund</p>
                                    </div>
                                </div>
                            </div>

                            {{-- NEW FIELDS - Stage 8: Activation & Units --}}
                            <div class="border-b pb-6 mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-2 border-b-2 border-gray-200">🚀 Stage 8: Investor Activation</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Units Allotted --}}
                                    <div>
                                        <label for="units_allotted" class="block text-sm font-medium text-gray-700 mb-2">
                                            Units/Shares Allotted
                                        </label>
                                        <input type="number" step="0.0001" name="units_allotted" id="units_allotted"
                                            value="{{ old('units_allotted', $investor->units_allotted) }}"
                                            placeholder="Number of units/shares"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <p class="mt-1 text-sm text-gray-500">Units assigned to investor</p>
                                    </div>

                                    {{-- Share Class --}}
                                    <div>
                                        <label for="share_class" class="block text-sm font-medium text-gray-700 mb-2">
                                            Share Class
                                        </label>
                                        <input type="text" name="share_class" id="share_class"
                                            value="{{ old('share_class', $investor->share_class) }}"
                                            placeholder="e.g., Class A, Class B, Founder"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <p class="mt-1 text-sm text-gray-500">Share class designation</p>
                                    </div>

                                    {{-- Welcome Letter Sent --}}
                                    <div>
                                        <label for="welcome_letter_sent_date" class="block text-sm font-medium text-gray-700 mb-2">
                                            Welcome Letter Sent Date
                                        </label>
                                        <input type="date" name="welcome_letter_sent_date" id="welcome_letter_sent_date"
                                            value="{{ old('welcome_letter_sent_date', $investor->welcome_letter_sent_date?->format('Y-m-d')) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    {{-- Investor Register Updated --}}
                                    <div class="flex items-start p-4 bg-blue-50 rounded-lg border border-blue-200">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="investor_register_updated" id="investor_register_updated"
                                                value="1" {{ $investor->investor_register_updated ? 'checked' : '' }}
                                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="investor_register_updated" class="font-medium text-gray-700">
                                                Investor Register Updated
                                            </label>
                                            <p class="text-gray-500">Investor added to official register</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- NEW FIELDS - CRM Workflow Discipline --}}
                            <div class="border-b pb-6 mt-6">
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                                    <p class="text-sm text-red-800">
                                        <strong>MANDATORY:</strong> Next action and due date must always be populated. No investor record should be without a clear next step.
                                    </p>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Next Action --}}
                                    <div>
                                        <label for="next_action" class="block text-sm font-medium text-gray-700 mb-2">
                                            Next Action <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="next_action" id="next_action" rows="3"
                                                placeholder="e.g., Schedule final approval meeting, Send welcome pack, Await capital call payment..."
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">{{ old('next_action', $investor->next_action) }}</textarea>
                                        <p class="mt-1 text-sm text-gray-500">What is the immediate next step for this investor?</p>
                                    </div>

                                    {{-- Next Action Due Date --}}
                                    <div>
                                        <label for="next_action_due_date" class="block text-sm font-medium text-gray-700 mb-2">
                                            Next Action Due Date
                                        </label>
                                        <input type="date" name="next_action_due_date" id="next_action_due_date"
                                            value="{{ old('next_action_due_date', $investor->next_action_due_date?->format('Y-m-d')) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                        <p class="mt-1 text-sm text-gray-500">When must this action be completed?</p>
                                    </div>
                                </div>
                            </div>

                            {{-- NEW FIELDS - Stage 9: Ongoing Monitoring --}}
                            <div class="border-b pb-6 mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-2 border-b-2 border-gray-200">🔄 Stage 9: Ongoing Monitoring</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    {{-- Last KYC Refresh --}}
                                    <div>
                                        <label for="last_kyc_refresh_date" class="block text-sm font-medium text-gray-700 mb-2">
                                            Last KYC Refresh Date
                                        </label>
                                        <input type="date" name="last_kyc_refresh_date" id="last_kyc_refresh_date"
                                            value="{{ old('last_kyc_refresh_date', $investor->last_kyc_refresh_date?->format('Y-m-d')) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    {{-- Next KYC Refresh Due --}}
                                    <div>
                                        <label for="next_kyc_refresh_due" class="block text-sm font-medium text-gray-700 mb-2">
                                            Next KYC Refresh Due
                                        </label>
                                        <input type="date" name="next_kyc_refresh_due" id="next_kyc_refresh_due"
                                            value="{{ old('next_kyc_refresh_due', $investor->next_kyc_refresh_due?->format('Y-m-d')) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    {{-- Last Sanctions Rescreen --}}
                                    <div>
                                        <label for="last_sanctions_rescreen_date" class="block text-sm font-medium text-gray-700 mb-2">
                                            Last Sanctions Rescreen
                                        </label>
                                        <input type="date" name="last_sanctions_rescreen_date" id="last_sanctions_rescreen_date"
                                            value="{{ old('last_sanctions_rescreen_date', $investor->last_sanctions_rescreen_date?->format('Y-m-d')) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>
                            {{-- ====== KRAJ DODAVANJA ====== --}}

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