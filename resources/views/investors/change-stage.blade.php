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
        // Stage requirements information
        const stageRequirements = {
            'prospect': {
                requirements: ['None - starting stage'],
                automation: 'No automatic actions'
            },
            'eligibility_review': {
                requirements: ['Target commitment amount ≥ $1M', 'Valid jurisdiction'],
                automation: 'Sanctions check timestamp will be recorded'
            },
            'ppm_issued': {
                requirements: ['Confirmed as professional client', 'Sanctions check passed'],
                automation: '✓ Data Room access granted (PROSPECT level)'
            },
            'kyc_in_progress': {
                requirements: ['PPM acknowledged'],
                automation: '✓ Data Room upgraded to QUALIFIED level'
            },
            'subscription_signed': {
                requirements: ['KYC status = Complete', 'Sanctions check passed'],
                automation: 'Subscription date recorded'
            },
            'approved': {
                requirements: ['Subscription signed', 'Final commitment amount > 0'],
                automation: 'Approval date & approver recorded'
            },
            'funded': {
                requirements: ['Approved', 'Bank account verified'],
                automation: 'Funding date recorded'
            },
            'active': {
                requirements: ['Funded amount > 0'],
                automation: '✓ Data Room upgraded to SUBSCRIBED level, ✓ Investor ID generated, ✓ Reporting access granted'
            },
            'monitored': {
                requirements: ['Active investor'],
                automation: 'Ongoing KYC/AML monitoring enabled'
            }
        };

        // Update stage info when selection changes
        document.getElementById('new_stage').addEventListener('change', function() {
            const stageValue = this.value;
            const infoDiv = document.getElementById('stage-info');
            
            if (stageValue && stageRequirements[stageValue]) {
                const info = stageRequirements[stageValue];
                infoDiv.innerHTML = `
                    <div class="space-y-3">
                        <div>
                            <p class="font-semibold text-gray-700 mb-2">Requirements:</p>
                            <ul class="list-disc list-inside space-y-1 text-gray-600">
                                ${info.requirements.map(req => `<li>${req}</li>`).join('')}
                            </ul>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-700 mb-1">Automatic Actions:</p>
                            <p class="text-gray-600">${info.automation}</p>
                        </div>
                    </div>
                `;
            } else {
                infoDiv.innerHTML = '<p class="text-gray-400 italic">Select a stage to see requirements...</p>';
            }
        });
    </script>

</x-app-layout>