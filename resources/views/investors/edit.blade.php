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

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <strong class="font-bold">Please fix the following errors:</strong>
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

                {{-- ===== CRM: ALWAYS VISIBLE ===== --}}
                <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-6 mb-6 shadow-sm">
                    <h3 class="text-sm font-bold text-red-700 uppercase tracking-wider mb-4">
                        Next Action — Required
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="next_action" class="block text-sm font-medium text-gray-700 mb-1">
                                Next Action <span class="text-red-500">*</span>
                            </label>
                            <textarea name="next_action" id="next_action" rows="2" required
                                      placeholder="e.g. Schedule KYC review call, Send NDA, Await capital call payment…"
                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-400 focus:ring-red-400 text-sm">{{ old('next_action', $investor->next_action) }}</textarea>
                        </div>
                        <div>
                            <label for="next_action_due_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Due Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="next_action_due_date" id="next_action_due_date" required
                                   value="{{ old('next_action_due_date', $investor->next_action_due_date?->format('Y-m-d')) }}"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-400 focus:ring-red-400 text-sm">
                            <p class="mt-1 text-xs text-gray-500">Every investor record must have a clear next step.</p>
                        </div>
                    </div>
                </div>

                {{-- ===== TABS ===== --}}
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">

                    {{-- Tab Navigation --}}
                    <div class="border-b border-gray-200 px-6 bg-gray-50">
                        <nav class="-mb-px flex space-x-1 overflow-x-auto" id="edit-tabs">
                            <button type="button" onclick="switchTab('profile')" id="tab-profile"
                                class="tab-btn whitespace-nowrap border-b-2 border-blue-500 text-blue-600 py-4 px-4 text-sm font-medium">
                                Profile
                            </button>
                            <button type="button" onclick="switchTab('eligibility')" id="tab-eligibility"
                                class="tab-btn whitespace-nowrap border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-4 text-sm font-medium">
                                Eligibility
                            </button>
                            <button type="button" onclick="switchTab('subscription')" id="tab-subscription"
                                class="tab-btn whitespace-nowrap border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-4 text-sm font-medium">
                                Subscription
                            </button>
                            <button type="button" onclick="switchTab('kyc')" id="tab-kyc"
                                class="tab-btn whitespace-nowrap border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-4 text-sm font-medium">
                                KYC & Approval
                            </button>
                            <button type="button" onclick="switchTab('activation')" id="tab-activation"
                                class="tab-btn whitespace-nowrap border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-4 text-sm font-medium">
                                Activation & Monitoring
                            </button>
                        </nav>
                    </div>

                    <div class="p-6">

                        {{-- ============================= --}}
                        {{-- TAB 1: PROFILE (Prospect)    --}}
                        {{-- ============================= --}}
                        <div id="pane-profile" class="tab-pane space-y-6">
                            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Stage: Prospect — Basic investor identity &amp; assignment</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="investor_type" class="block text-sm font-medium text-gray-700 mb-1">
                                        Investor Type <span class="text-red-500">*</span>
                                    </label>
                                    <select name="investor_type" id="investor_type" required
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="individual"    {{ $investor->investor_type === 'individual'    ? 'selected' : '' }}>Individual</option>
                                        <option value="corporate"     {{ $investor->investor_type === 'corporate'     ? 'selected' : '' }}>Corporate</option>
                                        <option value="family_office" {{ $investor->investor_type === 'family_office' ? 'selected' : '' }}>Family Office</option>
                                        <option value="spv"           {{ $investor->investor_type === 'spv'           ? 'selected' : '' }}>SPV</option>
                                        <option value="fund"          {{ $investor->investor_type === 'fund'          ? 'selected' : '' }}>Fund</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="jurisdiction" class="block text-sm font-medium text-gray-700 mb-1">
                                        Jurisdiction <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="jurisdiction" id="jurisdiction" required
                                           value="{{ old('jurisdiction', $investor->jurisdiction) }}"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>

                                <div>
                                    <label for="organization_name" class="block text-sm font-medium text-gray-700 mb-1">Organization Name</label>
                                    <input type="text" name="organization_name" id="organization_name"
                                           value="{{ old('organization_name', $investor->organization_name) }}"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>

                                <div>
                                    <label for="legal_entity_name" class="block text-sm font-medium text-gray-700 mb-1">Legal Entity Name</label>
                                    <input type="text" name="legal_entity_name" id="legal_entity_name"
                                           value="{{ old('legal_entity_name', $investor->legal_entity_name) }}"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>

                                <div>
                                    <label for="fund_id" class="block text-sm font-medium text-gray-700 mb-1">Fund</label>
                                    <select name="fund_id" id="fund_id"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">— Select Fund —</option>
                                        @foreach($funds as $fund)
                                            <option value="{{ $fund->id }}" {{ $investor->fund_id == $fund->id ? 'selected' : '' }}>
                                                {{ $fund->fund_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="assigned_to_user_id" class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                                    <select name="assigned_to_user_id" id="assigned_to_user_id"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">— Select User —</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $investor->assigned_to_user_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="border-t pt-6 space-y-4">
                                <div>
                                    <label for="source_of_introduction" class="block text-sm font-medium text-gray-700 mb-1">Source of Introduction</label>
                                    <select name="source_of_introduction" id="source_of_introduction"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">— Select Source —</option>
                                        <option value="direct"           {{ $investor->source_of_introduction === 'direct'           ? 'selected' : '' }}>Direct</option>
                                        <option value="advisor"          {{ $investor->source_of_introduction === 'advisor'          ? 'selected' : '' }}>Advisor</option>
                                        <option value="placement_agent"  {{ $investor->source_of_introduction === 'placement_agent'  ? 'selected' : '' }}>Placement Agent</option>
                                        <option value="referral"         {{ $investor->source_of_introduction === 'referral'         ? 'selected' : '' }}>Referral</option>
                                        <option value="event"            {{ $investor->source_of_introduction === 'event'            ? 'selected' : '' }}>Event</option>
                                        <option value="other"            {{ $investor->source_of_introduction === 'other'            ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>

                                <div id="referral-source-field" style="display: {{ $investor->source_of_introduction === 'referral' ? 'block' : 'none' }};">
                                    <label for="referral_source" class="block text-sm font-medium text-gray-700 mb-1">Referral Source Details</label>
                                    <input type="text" name="referral_source" id="referral_source"
                                           value="{{ old('referral_source', $investor->referral_source) }}"
                                           placeholder="Name of person/company who referred"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>

                                <div id="placement-agent-fields" style="display: {{ $investor->source_of_introduction === 'placement_agent' ? 'block' : 'none' }};" class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Placement Agent Name</label>
                                        <input type="text" name="placement_agent_name"
                                               value="{{ old('placement_agent_name', $investor->placement_agent_name) }}"
                                               placeholder="Placement agent company or individual name"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Placement Agent Email</label>
                                        <input type="email" name="placement_agent_email"
                                               value="{{ old('placement_agent_email', $investor->placement_agent_email) }}"
                                               placeholder="agent@example.com"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                </div>

                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" id="notes" rows="3"
                                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('notes', $investor->notes) }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- ======================================== --}}
                        {{-- TAB 2: ELIGIBILITY (→ Eligibility Confirmed) --}}
                        {{-- ======================================== --}}
                        <div id="pane-eligibility" class="tab-pane hidden space-y-6">

                            {{-- DIFC prompt banner (shown after document access approval) --}}
                            <div id="difc-prompt-banner" class="hidden bg-amber-50 border-l-4 border-amber-400 rounded-lg p-4">
                                <div class="flex items-start">
                                    <svg class="h-5 w-5 text-amber-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-amber-800">DIFC Data Protection Consent Required</p>
                                        <p class="text-sm text-amber-700 mt-1">
                                            This investor has just requested access to documents.
                                            Under DIFC Data Protection Law, <strong>requesting access to documents constitutes consent</strong> to the processing of their personal data.
                                            Please confirm by checking the box below.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Stage: Eligibility Confirmed — All gates must be met to progress</p>

                            {{-- Financial gate --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="target_commitment_amount" class="block text-sm font-medium text-gray-700 mb-1">
                                        Target Commitment Amount
                                        <span class="ml-2 text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Gate</span>
                                    </label>
                                    <input type="number" step="0.01" name="target_commitment_amount" id="target_commitment_amount"
                                           value="{{ old('target_commitment_amount', $investor->target_commitment_amount) }}"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <p class="mt-1 text-xs text-gray-500">Minimum $1,000,000</p>
                                </div>
                                <div>
                                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                                    <select name="currency" id="currency"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="USD" {{ $investor->currency === 'USD' ? 'selected' : '' }}>USD</option>
                                        <option value="EUR" {{ $investor->currency === 'EUR' ? 'selected' : '' }}>EUR</option>
                                        <option value="GBP" {{ $investor->currency === 'GBP' ? 'selected' : '' }}>GBP</option>
                                        <option value="AED" {{ $investor->currency === 'AED' ? 'selected' : '' }}>AED</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Eligibility gates --}}
                            <div class="border border-gray-200 rounded-lg divide-y divide-gray-100">
                                <div class="px-4 py-2 bg-gray-50 rounded-t-lg">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Eligibility Gates</span>
                                </div>

                                <div class="flex items-start px-4 py-4">
                                    <input type="checkbox" name="is_professional_client" id="is_professional_client" value="1"
                                           {{ $investor->is_professional_client ? 'checked' : '' }}
                                           class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <div class="ml-3">
                                        <label for="is_professional_client" class="text-sm font-medium text-gray-700">
                                            Professional Client Status Confirmed
                                            <span class="ml-2 text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Gate</span>
                                        </label>
                                        <p class="text-xs text-gray-500 mt-0.5">Investor meets professional/qualified investor criteria (verbally confirmed)</p>
                                        @if($investor->confirmed_professional_client_at)
                                            <p class="text-xs text-green-600 mt-1">✓ Confirmed {{ $investor->confirmed_professional_client_at->format('d M Y, H:i') }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-start px-4 py-4">
                                    <input type="checkbox" name="difc_dp_consent" id="difc_dp_consent" value="1"
                                           {{ $investor->difc_dp_consent ? 'checked' : '' }}
                                           class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <div class="ml-3">
                                        <label for="difc_dp_consent" class="text-sm font-medium text-gray-700">
                                            DIFC Data Protection Consent Confirmed
                                            <span class="ml-2 text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Gate</span>
                                        </label>
                                        <p class="text-xs text-gray-500 mt-0.5">Investor has consented to data processing under DIFC DP Law — confirmed by requesting document access</p>
                                        @if($investor->difc_dp_consent_at)
                                            <p class="text-xs text-green-600 mt-1">✓ Confirmed {{ $investor->difc_dp_consent_at->format('d M Y, H:i') }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-start px-4 py-4">
                                    <input type="checkbox" name="agreed_confidentiality" id="agreed_confidentiality" value="1"
                                           {{ $investor->agreed_confidentiality ? 'checked' : '' }}
                                           class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <div class="ml-3">
                                        <label for="agreed_confidentiality" class="text-sm font-medium text-gray-700">
                                            NDA / Confidentiality Accepted
                                            <span class="ml-2 text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Gate</span>
                                        </label>
                                        <p class="text-xs text-gray-500 mt-0.5">Access request logged — investor accepted NDA terms</p>
                                        @if($investor->agreed_confidentiality_at)
                                            <p class="text-xs text-green-600 mt-1">✓ Confirmed {{ $investor->agreed_confidentiality_at->format('d M Y, H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Risk assessment --}}
                            <div class="border-t pt-6">
                                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-4">Risk Assessment</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="risk_profile" class="block text-sm font-medium text-gray-700 mb-1">Risk Profile</label>
                                        <select name="risk_profile" id="risk_profile"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <option value="">— Select —</option>
                                            <option value="low"    {{ $investor->risk_profile === 'low'    ? 'selected' : '' }}>Low</option>
                                            <option value="medium" {{ $investor->risk_profile === 'medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="high"   {{ $investor->risk_profile === 'high'   ? 'selected' : '' }}>High</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="investor_experience" class="block text-sm font-medium text-gray-700 mb-1">Investor Experience</label>
                                        <textarea name="investor_experience" id="investor_experience" rows="3"
                                                  placeholder="Describe investor's experience with alternative investments…"
                                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('investor_experience', $investor->investor_experience) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ============================================= --}}
                        {{-- TAB 3: SUBSCRIPTION (→ Portal Access Granted) --}}
                        {{-- ============================================= --}}
                        <div id="pane-subscription" class="tab-pane hidden space-y-6">
                            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Stage: Portal Access Granted — Subscription &amp; legal documentation</p>

                            {{-- Subscription gates --}}
                            <div class="border border-gray-200 rounded-lg divide-y divide-gray-100">
                                <div class="px-4 py-2 bg-gray-50 rounded-t-lg">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Subscription Gates</span>
                                </div>

                                <div class="flex items-start px-4 py-4">
                                    <input type="checkbox" name="subscription_signed" id="subscription_signed" value="1"
                                           {{ $investor->subscription_signed_date ? 'checked' : '' }}
                                           class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <div class="ml-3">
                                        <label for="subscription_signed" class="text-sm font-medium text-gray-700">
                                            Subscription Agreement Signed &amp; Received
                                            <span class="ml-2 text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Gate</span>
                                        </label>
                                        <p class="text-xs text-gray-500 mt-0.5">Investor has signed and returned the subscription agreement</p>
                                        @if($investor->subscription_signed_date)
                                            <p class="text-xs text-green-600 mt-1">✓ Signed {{ $investor->subscription_signed_date->format('d M Y, H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="final_commitment_amount" class="block text-sm font-medium text-gray-700 mb-1">
                                        Final Commitment Amount
                                        <span class="ml-2 text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Gate</span>
                                    </label>
                                    <input type="number" step="0.01" name="final_commitment_amount" id="final_commitment_amount"
                                           value="{{ old('final_commitment_amount', $investor->final_commitment_amount) }}"
                                           placeholder="Actual committed amount after subscription"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>
                            </div>

                            {{-- PPM & Legal --}}
                            <div class="border-t pt-6">
                                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-4">PPM &amp; Legal Review</p>

                                <div class="border border-gray-200 rounded-lg divide-y divide-gray-100 mb-4">
                                    <div class="flex items-start px-4 py-4">
                                        <input type="checkbox" name="ppm_acknowledged" id="ppm_acknowledged" value="1"
                                               {{ $investor->ppm_acknowledged_date ? 'checked' : '' }}
                                               class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <div class="ml-3">
                                            <label for="ppm_acknowledged" class="text-sm font-medium text-gray-700">PPM Acknowledged</label>
                                            <p class="text-xs text-gray-500 mt-0.5">Investor has acknowledged PPM — recorded automatically on Portal Access Granted</p>
                                            @if($investor->ppm_acknowledged_date)
                                                <p class="text-xs text-green-600 mt-1">✓ Acknowledged {{ $investor->ppm_acknowledged_date->format('d M Y, H:i') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-start px-4 py-4">
                                        <input type="checkbox" name="acknowledged_ppm_confidential" id="acknowledged_ppm_confidential" value="1"
                                               {{ $investor->acknowledged_ppm_confidential ? 'checked' : '' }}
                                               class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <div class="ml-3">
                                            <label for="acknowledged_ppm_confidential" class="text-sm font-medium text-gray-700">PPM Confidentiality Acknowledged</label>
                                            <p class="text-xs text-gray-500 mt-0.5">Investor acknowledged confidentiality obligations within the PPM</p>
                                            @if($investor->acknowledged_ppm_confidential_at)
                                                <p class="text-xs text-green-600 mt-1">✓ Acknowledged {{ $investor->acknowledged_ppm_confidential_at->format('d M Y, H:i') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-start px-4 py-4">
                                        <input type="checkbox" name="legal_review_complete" id="legal_review_complete" value="1"
                                               {{ $investor->legal_review_complete ? 'checked' : '' }}
                                               class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <div class="ml-3">
                                            <label for="legal_review_complete" class="text-sm font-medium text-gray-700">Legal Review Complete</label>
                                            <p class="text-xs text-gray-500 mt-0.5">All subscription documents reviewed and approved by legal team</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Side letter --}}
                                <div class="border border-gray-200 rounded-lg px-4 py-4">
                                    <div class="flex items-start">
                                        <input type="checkbox" name="side_letter_exists" id="side_letter_exists" value="1"
                                               {{ $investor->side_letter_exists ? 'checked' : '' }}
                                               class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <div class="ml-3 flex-1">
                                            <label for="side_letter_exists" class="text-sm font-medium text-gray-700">Side Letter Exists</label>
                                            <p class="text-xs text-gray-500 mt-0.5">Investor has negotiated special terms via side letter</p>
                                            <div id="side_letter_terms_field" class="mt-3" style="display: {{ $investor->side_letter_exists ? 'block' : 'none' }};">
                                                <label for="side_letter_terms" class="block text-sm font-medium text-gray-700 mb-1">Side Letter Key Terms</label>
                                                <textarea name="side_letter_terms" id="side_letter_terms" rows="3"
                                                          placeholder="Summarize key terms (fees, reporting, governance rights, etc.)"
                                                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('side_letter_terms', $investor->side_letter_terms) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ============================================= --}}
                        {{-- TAB 4: KYC & APPROVAL                         --}}
                        {{-- ============================================= --}}
                        <div id="pane-kyc" class="tab-pane hidden space-y-6">
                            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Stages: KYC In Progress → KYC Completed / Approved</p>

                            {{-- KYC Status --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="kyc_status" class="block text-sm font-medium text-gray-700 mb-1">
                                        KYC Status
                                        <span class="ml-2 text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Gate</span>
                                    </label>
                                    <select name="kyc_status" id="kyc_status"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">— Not Started —</option>
                                        <option value="in_progress"   {{ $investor->kyc_status === 'in_progress'   ? 'selected' : '' }}>In Progress</option>
                                        <option value="submitted"     {{ $investor->kyc_status === 'submitted'     ? 'selected' : '' }}>Submitted</option>
                                        <option value="under_review"  {{ $investor->kyc_status === 'under_review'  ? 'selected' : '' }}>Under Review</option>
                                        <option value="complete"      {{ $investor->kyc_status === 'complete'      ? 'selected' : '' }}>Complete</option>
                                        <option value="rejected"      {{ $investor->kyc_status === 'rejected'      ? 'selected' : '' }}>Rejected</option>
                                        <option value="expired"       {{ $investor->kyc_status === 'expired'       ? 'selected' : '' }}>Expired</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">Set to "In Progress" to enter KYC In Progress stage; "Complete" to proceed to KYC Completed</p>
                                </div>
                                <div>
                                    <label for="kyc_risk_rating" class="block text-sm font-medium text-gray-700 mb-1">KYC Risk Rating</label>
                                    <select name="kyc_risk_rating" id="kyc_risk_rating"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">— Not Rated —</option>
                                        <option value="low"    {{ $investor->kyc_risk_rating === 'low'    ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ $investor->kyc_risk_rating === 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high"   {{ $investor->kyc_risk_rating === 'high'   ? 'selected' : '' }}>High</option>
                                    </select>
                                </div>
                            </div>

                            {{-- KYC Completed/Approved gates --}}
                            <div class="border border-gray-200 rounded-lg divide-y divide-gray-100">
                                <div class="px-4 py-2 bg-gray-50 rounded-t-lg">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">KYC Completed / Approved Gates</span>
                                </div>

                                <div class="flex items-start px-4 py-4">
                                    <input type="checkbox" name="sanctions_check_passed" id="sanctions_check_passed" value="1"
                                           {{ $investor->sanctions_check_passed ? 'checked' : '' }}
                                           class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <div class="ml-3">
                                        <label for="sanctions_check_passed" class="text-sm font-medium text-gray-700">
                                            Sanctions Check Passed
                                            <span class="ml-2 text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Gate</span>
                                        </label>
                                        <p class="text-xs text-gray-500 mt-0.5">Investor cleared sanctions and PEP screening</p>
                                        @if($investor->sanctions_checked_at)
                                            <p class="text-xs text-green-600 mt-1">✓ Checked {{ $investor->sanctions_checked_at->format('d M Y, H:i') }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-start px-4 py-4">
                                    <input type="checkbox" name="commitment_letter_signed" id="commitment_letter_signed" value="1"
                                           {{ $investor->commitment_letter_signed ? 'checked' : '' }}
                                           class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <div class="ml-3">
                                        <label for="commitment_letter_signed" class="text-sm font-medium text-gray-700">
                                            Commitment Letter Signed
                                            <span class="ml-2 text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Gate</span>
                                        </label>
                                        <p class="text-xs text-gray-500 mt-0.5">Investor has signed the formal commitment letter</p>
                                        @if($investor->commitment_letter_signed_at)
                                            <p class="text-xs text-green-600 mt-1">✓ Signed {{ $investor->commitment_letter_signed_at->format('d M Y, H:i') }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-start px-4 py-4">
                                    @php $eddChecked = $investor->enhanced_due_diligence_required ?? false; @endphp
                                    <input type="checkbox" name="enhanced_due_diligence_required" id="enhanced_due_diligence_required" value="1"
                                           {{ $eddChecked ? 'checked' : '' }}
                                           class="mt-0.5 h-4 w-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                                    <div class="ml-3">
                                        <label for="enhanced_due_diligence_required" class="text-sm font-medium text-gray-700">Enhanced Due Diligence Required</label>
                                        <p class="text-xs text-gray-500 mt-0.5">High-risk investor requires additional verification procedures</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Approval & Governance --}}
                            <div class="border-t pt-6">
                                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-4">Approval &amp; Governance</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="border border-gray-200 rounded-lg px-4 py-4">
                                        <div class="flex items-start">
                                            <input type="checkbox" name="board_approval_required" id="board_approval_required" value="1"
                                                   {{ $investor->board_approval_required ? 'checked' : '' }}
                                                   class="mt-0.5 h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                            <div class="ml-3 flex-1">
                                                <label for="board_approval_required" class="text-sm font-medium text-gray-700">Board / IC Approval Required</label>
                                                <p class="text-xs text-gray-500 mt-0.5">Large commitment requires board or investment committee approval</p>
                                                <div id="board_approval_date_field" class="mt-3" style="display: {{ $investor->board_approval_required ? 'block' : 'none' }};">
                                                    <label for="board_approval_date" class="block text-sm font-medium text-gray-700 mb-1">Approval Date</label>
                                                    <input type="date" name="board_approval_date" id="board_approval_date"
                                                           value="{{ old('board_approval_date', $investor->board_approval_date?->format('Y-m-d')) }}"
                                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="admission_notice_issued_date" class="block text-sm font-medium text-gray-700 mb-1">Admission Notice Issued Date</label>
                                        <input type="date" name="admission_notice_issued_date" id="admission_notice_issued_date"
                                               value="{{ old('admission_notice_issued_date', $investor->admission_notice_issued_date?->format('Y-m-d')) }}"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <p class="mt-1 text-xs text-gray-500">Date investor formally admitted to the fund</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ============================================= --}}
                        {{-- TAB 5: ACTIVATION & MONITORING                --}}
                        {{-- ============================================= --}}
                        <div id="pane-activation" class="tab-pane hidden space-y-6">
                            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Stages: Funded / Active → Monitored</p>

                            {{-- Funded/Active gates --}}
                            <div class="border border-gray-200 rounded-lg divide-y divide-gray-100">
                                <div class="px-4 py-2 bg-gray-50 rounded-t-lg">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Funded / Active Gates</span>
                                </div>

                                <div class="flex items-start px-4 py-4">
                                    <input type="checkbox" name="bank_account_verified" id="bank_account_verified" value="1"
                                           {{ $investor->bank_account_verified ? 'checked' : '' }}
                                           class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <div class="ml-3">
                                        <label for="bank_account_verified" class="text-sm font-medium text-gray-700">
                                            Bank Account Verified
                                            <span class="ml-2 text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Gate</span>
                                        </label>
                                        <p class="text-xs text-gray-500 mt-0.5">Bank account details confirmed and validated</p>
                                        @if($investor->bank_verified_date)
                                            <p class="text-xs text-green-600 mt-1">✓ Verified {{ $investor->bank_verified_date->format('d M Y, H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="funded_amount" class="block text-sm font-medium text-gray-700 mb-1">
                                        Funded Amount
                                        <span class="ml-2 text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Gate</span>
                                    </label>
                                    <input type="number" step="0.01" name="funded_amount" id="funded_amount"
                                           value="{{ old('funded_amount', $investor->funded_amount) }}"
                                           placeholder="Total amount actually received"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <p class="mt-1 text-xs text-gray-500">Must be entered to progress to Funded / Active stage</p>
                                </div>
                            </div>

                            {{-- Activation details --}}
                            <div class="border-t pt-6">
                                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-4">Activation Details</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="units_allotted" class="block text-sm font-medium text-gray-700 mb-1">Units / Shares Allotted</label>
                                        <input type="number" step="0.0001" name="units_allotted" id="units_allotted"
                                               value="{{ old('units_allotted', $investor->units_allotted) }}"
                                               placeholder="Number of units/shares"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label for="share_class" class="block text-sm font-medium text-gray-700 mb-1">Share Class</label>
                                        <input type="text" name="share_class" id="share_class"
                                               value="{{ old('share_class', $investor->share_class) }}"
                                               placeholder="e.g. Class A, Class B, Founder"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label for="welcome_letter_sent_date" class="block text-sm font-medium text-gray-700 mb-1">Welcome Letter Sent Date</label>
                                        <input type="date" name="welcome_letter_sent_date" id="welcome_letter_sent_date"
                                               value="{{ old('welcome_letter_sent_date', $investor->welcome_letter_sent_date?->format('Y-m-d')) }}"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div class="flex items-start pt-6">
                                        <input type="checkbox" name="investor_register_updated" id="investor_register_updated" value="1"
                                               {{ $investor->investor_register_updated ? 'checked' : '' }}
                                               class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <div class="ml-3">
                                            <label for="investor_register_updated" class="text-sm font-medium text-gray-700">Investor Register Updated</label>
                                            <p class="text-xs text-gray-500 mt-0.5">Investor added to the official register</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Ongoing monitoring --}}
                            <div class="border-t pt-6">
                                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-4">Ongoing Monitoring — Stage: Monitored</p>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label for="last_kyc_refresh_date" class="block text-sm font-medium text-gray-700 mb-1">Last KYC Refresh</label>
                                        <input type="date" name="last_kyc_refresh_date" id="last_kyc_refresh_date"
                                               value="{{ old('last_kyc_refresh_date', $investor->last_kyc_refresh_date?->format('Y-m-d')) }}"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label for="next_kyc_refresh_due" class="block text-sm font-medium text-gray-700 mb-1">Next KYC Refresh Due</label>
                                        <input type="date" name="next_kyc_refresh_due" id="next_kyc_refresh_due"
                                               value="{{ old('next_kyc_refresh_due', $investor->next_kyc_refresh_due?->format('Y-m-d')) }}"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label for="last_sanctions_rescreen_date" class="block text-sm font-medium text-gray-700 mb-1">Last Sanctions Re-screen</label>
                                        <input type="date" name="last_sanctions_rescreen_date" id="last_sanctions_rescreen_date"
                                               value="{{ old('last_sanctions_rescreen_date', $investor->last_sanctions_rescreen_date?->format('Y-m-d')) }}"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>{{-- /p-6 --}}
                </div>{{-- /tab card --}}

                {{-- Submit --}}
                <div class="mt-6 flex items-center justify-end space-x-3">
                    <a href="{{ route('investors.show', $investor) }}"
                       class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded">
                        Update Investor
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script>
        function switchTab(name) {
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('border-blue-500', 'text-blue-600');
                b.classList.add('border-transparent', 'text-gray-500');
            });
            document.getElementById('pane-' + name).classList.remove('hidden');
            const btn = document.getElementById('tab-' + name);
            btn.classList.remove('border-transparent', 'text-gray-500');
            btn.classList.add('border-blue-500', 'text-blue-600');
            window.location.hash = name;
        }

        // Source of introduction toggle
        function updateSourceFields(value) {
            document.getElementById('referral-source-field').style.display    = value === 'referral'         ? 'block' : 'none';
            document.getElementById('placement-agent-fields').style.display   = value === 'placement_agent'  ? 'block' : 'none';
        }
        document.getElementById('source_of_introduction').addEventListener('change', function () {
            updateSourceFields(this.value);
        });

        // Side letter terms toggle
        document.getElementById('side_letter_exists').addEventListener('change', function () {
            document.getElementById('side_letter_terms_field').style.display = this.checked ? 'block' : 'none';
        });

        // Board approval date toggle
        document.getElementById('board_approval_required').addEventListener('change', function () {
            document.getElementById('board_approval_date_field').style.display = this.checked ? 'block' : 'none';
        });

        // Restore tab from URL hash
        const hash = window.location.hash.replace('#', '');
        const validTabs = ['profile', 'eligibility', 'subscription', 'kyc', 'activation'];
        if (hash && validTabs.includes(hash)) {
            switchTab(hash);
        }

        @if(session('difc_consent_prompt'))
            // Redirect from document access approval — show DIFC consent prompt
            switchTab('eligibility');
            document.getElementById('difc-prompt-banner').classList.remove('hidden');
            document.getElementById('difc-prompt-banner').scrollIntoView({ behavior: 'smooth', block: 'start' });
        @endif
    </script>

</x-app-layout>
