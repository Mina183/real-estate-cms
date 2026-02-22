<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Change Stage: {{ $investor->organization_name ?? $investor->legal_entity_name }}
            </h2>
            <a href="{{ route('investors.show', $investor) }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Cancel
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Current Stage Display -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Current Stage</h3>
                        <div class="flex items-center space-x-4">
                            <span class="px-4 py-2 inline-flex text-sm font-semibold rounded-full 
                                @if($investor->stage === 'prospect') bg-gray-100 text-gray-800
                                @elseif($investor->stage === 'eligibility_review') bg-yellow-100 text-yellow-800
                                @elseif($investor->stage === 'ppm_issued') bg-blue-100 text-blue-800
                                @elseif($investor->stage === 'kyc_in_progress') bg-purple-100 text-purple-800
                                @elseif($investor->stage === 'subscription_signed') bg-indigo-100 text-indigo-800
                                @elseif($investor->stage === 'approved') bg-green-100 text-green-800
                                @elseif($investor->stage === 'funded') bg-teal-100 text-teal-800
                                @elseif($investor->stage === 'active') bg-emerald-100 text-emerald-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ str_replace('_', ' ', ucfirst($investor->stage)) }}
                            </span>
                            <span class="text-gray-600">→</span>
                            <span class="text-gray-500">Moving to new stage...</span>
                        </div>
                    </div>

                    <!-- Error Display -->
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            @if($errors->has('requirements'))
                                <strong class="font-bold">{{ $errors->first('requirements') }}</strong>
                            @endif
                            
                            @if($errors->has('missing'))
                                <ul class="mt-2 list-disc list-inside">
                                    @foreach (session('missing_requirements', []) as $requirement)
                                        <li>{{ $requirement }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            
                            @if($errors->has('error'))
                                <p class="mt-2">{{ $errors->first('error') }}</p>
                            @endif
                            
                            @if(!$errors->has('requirements') && !$errors->has('missing') && !$errors->has('error'))
                                <p>{{ $errors->first() }}</p>
                            @endif

                            @if(session('missing_requirements'))
                                <ul class="mt-3 list-disc list-inside space-y-1">
                                    @foreach (session('missing_requirements', []) as $requirement)
                                        <li class="text-sm">{{ $requirement }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif

                    <!-- Stage Change Form -->
                    <form action="{{ route('investors.change-stage', $investor) }}" method="POST">
                        @csrf

                        <div class="space-y-6">
                            
                            <!-- New Stage Selection -->
                            <div>
                                <label for="new_stage" class="block text-sm font-medium text-gray-700 mb-2">
                                    Select New Stage <span class="text-red-500">*</span>
                                </label>
                                <select name="new_stage" id="new_stage" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg py-3">
                                    <option value="">-- Select Stage --</option>
                                    @foreach($stages as $value => $label)
                                        <option value="{{ $value }}" 
                                                {{ old('new_stage') === $value ? 'selected' : '' }}
                                                {{ $investor->stage === $value ? 'disabled' : '' }}>
                                            {{ $label }}
                                            {{ $investor->stage === $value ? '(Current)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-2 text-sm text-gray-500">
                                    Choose the stage you want to move this investor to
                                </p>
                            </div>

                            <!-- Stage Information -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-900 mb-3">Stage Requirements:</h4>
                                <div id="stage-info" class="text-sm text-gray-600">
                                    <p class="text-gray-400 italic">Select a stage to see requirements...</p>
                                </div>
                            </div>

                             <!-- Current Investor Status -->
                            <div class="mt-4 pt-4 mb-6 border-t border-gray-300">
                                <h5 class="font-semibold text-gray-700 mb-2">Current Investor Status:</h5>
                                <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                                    <div class="flex items-center">
                                        <span class="{{ $investor->target_commitment_amount >= 1000000 ? 'text-green-600' : 'text-red-600' }} mr-2">
                                            {{ $investor->target_commitment_amount >= 1000000 ? '✓' : '✗' }}
                                        </span>
                                        <span>Target Commitment: {{ $investor->currency }} {{ number_format($investor->target_commitment_amount ?? 0) }}</span>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <span class="{{ $investor->is_professional_client ? 'text-green-600' : 'text-red-600' }} mr-2">
                                            {{ $investor->is_professional_client ? '✓' : '✗' }}
                                        </span>
                                        <span>Professional Client</span>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <span class="{{ $investor->sanctions_check_passed ? 'text-green-600' : 'text-red-600' }} mr-2">
                                            {{ $investor->sanctions_check_passed ? '✓' : '✗' }}
                                        </span>
                                        <span>Sanctions Check</span>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <span class="{{ $investor->ppm_acknowledged_date ? 'text-green-600' : 'text-red-600' }} mr-2">
                                            {{ $investor->ppm_acknowledged_date ? '✓' : '✗' }}
                                        </span>
                                        <span>PPM Acknowledged</span>
                                    </div>

                                    <div class="flex items-center">
                                        <span class="{{ $investor->confidentiality_acknowledged ? 'text-green-600' : 'text-red-600' }} mr-2">
                                            {{ $investor->confidentiality_acknowledged ? '✓' : '✗' }}
                                        </span>
                                        <span>Confidentiality Agreement</span>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <span class="{{ $investor->kyc_status === 'complete' ? 'text-green-600' : 'text-red-600' }} mr-2">
                                            {{ $investor->kyc_status === 'complete' ? '✓' : '✗' }}
                                        </span>
                                        <span>KYC Complete</span>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <span class="{{ $investor->subscription_signed_date ? 'text-green-600' : 'text-red-600' }} mr-2">
                                            {{ $investor->subscription_signed_date ? '✓' : '✗' }}
                                        </span>
                                        <span>Subscription Signed</span>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <span class="{{ $investor->final_commitment_amount > 0 ? 'text-green-600' : 'text-red-600' }} mr-2">
                                            {{ $investor->final_commitment_amount > 0 ? '✓' : '✗' }}
                                        </span>
                                        <span>Final Commitment Set</span>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <span class="{{ $investor->bank_account_verified ? 'text-green-600' : 'text-red-600' }} mr-2">
                                            {{ $investor->bank_account_verified ? '✓' : '✗' }}
                                        </span>
                                        <span>Bank Verified</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <!-- Reason for Change -->
                            <div>
                                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                                    Reason for Change (Optional)
                                </label>
                                <textarea name="reason" id="reason" rows="4"
                                          placeholder="e.g., Completed KYC documents, Signed subscription agreement, etc."
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('reason') }}</textarea>
                                <p class="mt-2 text-sm text-gray-500">
                                    Provide context for this stage change (will be logged in audit trail)
                                </p>
                            </div>

                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 flex items-center justify-end space-x-3">
                            <a href="{{ route('investors.show', $investor) }}" 
                               class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded">
                                Change Stage →
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            <!-- Stage Progression Visual Guide -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Stage Progression Guide</h3>
                    <div class="flex items-center space-x-2 overflow-x-auto pb-2">
                        @foreach($stages as $value => $label)
                            <div class="flex items-center">
                                <div class="px-3 py-2 rounded text-xs font-semibold whitespace-nowrap
                                    {{ $investor->stage === $value ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                                    {{ $label }}
                                </div>
                                @if(!$loop->last)
                                    <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>

<script>
    // Dynamic stage requirements from backend
    const stageRequirements = @json($stageRequirements);

    // Update stage info when selection changes
    document.getElementById('new_stage').addEventListener('change', function() {
        const stageValue = this.value;
        const infoDiv = document.getElementById('stage-info');
        
        if (stageValue && stageRequirements[stageValue]) {
            const info = stageRequirements[stageValue];
            
            // Build requirements list
            let requirementsHtml = '<p class="text-sm text-gray-500 italic">All requirements met ✓</p>';
            
            if (info.requirements && info.requirements.length > 0) {
                requirementsHtml = `
                    <ul class="list-disc list-inside space-y-1 text-red-600">
                        ${info.requirements.map(req => `<li class="text-sm">${req}</li>`).join('')}
                    </ul>
                `;
            }
            
            infoDiv.innerHTML = `
                <div class="space-y-3">
                    <div>
                        <p class="font-semibold text-gray-700 mb-2">Requirements:</p>
                        ${requirementsHtml}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-700 mb-1">Automatic Actions:</p>
                        <p class="text-sm text-gray-600">${info.automation || 'None'}</p>
                    </div>
                </div>
            `;
        } else {
            infoDiv.innerHTML = '<p class="text-gray-400 italic">Select a stage to see requirements...</p>';
        }
    });
</script>

</x-app-layout>