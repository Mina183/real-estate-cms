<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $investor->organization_name ?? $investor->legal_entity_name ?? 'Investor Details' }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('investors.edit', $investor) }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                <a href="{{ route('investors.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    ← Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Basic Information Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Investor Type</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($investor->investor_type) }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Jurisdiction</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $investor->jurisdiction }}</p>
                        </div>

                        @if($investor->organization_name)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Organization Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $investor->organization_name }}</p>
                        </div>
                        @endif

                        @if($investor->legal_entity_name)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Legal Entity Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $investor->legal_entity_name }}</p>
                        </div>
                        @endif

                        @if($investor->fund)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Fund</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $investor->fund->fund_name }}</p>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Assigned To</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $investor->assignedTo->name ?? 'Unassigned' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Workflow Status Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Workflow Status</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Stage</label>
                            <p class="mt-1">
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full 
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
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <p class="mt-1">
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full 
                                    @if($investor->status === 'pending') bg-gray-100 text-gray-800
                                    @elseif($investor->status === 'in_review') bg-yellow-100 text-yellow-800
                                    @elseif($investor->status === 'qualified') bg-green-100 text-green-800
                                    @elseif($investor->status === 'action_required') bg-red-100 text-red-800
                                    @elseif($investor->status === 'on_hold') bg-orange-100 text-orange-800
                                    @elseif($investor->status === 'rejected') bg-red-100 text-red-800
                                    @endif">
                                    {{ str_replace('_', ' ', ucfirst($investor->status)) }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Lifecycle Status</label>
                            <p class="mt-1">
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full 
                                    @if($investor->lifecycle_status === 'active') bg-green-100 text-green-800
                                    @elseif($investor->lifecycle_status === 'inactive') bg-gray-100 text-gray-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($investor->lifecycle_status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Information Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Financial Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @if($investor->target_commitment_amount)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Target Commitment</label>
                            <p class="mt-1 text-sm text-gray-900 font-semibold">
                                {{ $investor->currency }} {{ number_format($investor->target_commitment_amount, 2) }}
                            </p>
                        </div>
                        @endif

                        @if($investor->final_commitment_amount)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Final Commitment</label>
                            <p class="mt-1 text-sm text-gray-900 font-semibold">
                                {{ $investor->currency }} {{ number_format($investor->final_commitment_amount, 2) }}
                            </p>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Funded Amount</label>
                            <p class="mt-1 text-sm text-gray-900 font-semibold">
                                {{ $investor->currency }} {{ number_format($investor->funded_amount ?? 0, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contacts Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Contacts</h3>
                        <button class="bg-blue-500 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded">
                            + Add Contact
                        </button>
                    </div>
                    
                    @if($investor->contacts->count() > 0)
                        <div class="space-y-3">
                            @foreach($investor->contacts as $contact)
                                <div class="border border-gray-200 rounded p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $contact->full_name }}</p>
                                            <p class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $contact->role)) }}</p>
                                            @if($contact->is_primary)
                                                <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Primary Contact
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-right text-sm text-gray-600">
                                            @if($contact->email)
                                                <p>{{ $contact->email }}</p>
                                            @endif
                                            @if($contact->phone)
                                                <p>{{ $contact->phone }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No contacts added yet.</p>
                    @endif
                </div>
            </div>

            <!-- Source & Notes Card -->
            @if($investor->source_of_introduction || $investor->notes)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
                    
                    @if($investor->source_of_introduction)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-500">Source of Introduction</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ ucfirst(str_replace('_', ' ', $investor->source_of_introduction)) }}
                            @if($investor->referral_source)
                                ({{ $investor->referral_source }})
                            @endif
                        </p>
                    </div>
                    @endif

                    @if($investor->notes)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Notes</label>
                        <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $investor->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Metadata Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">System Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Created At</label>
                            <p class="mt-1 text-gray-900">{{ $investor->created_at->format('M d, Y H:i') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Created By</label>
                            <p class="mt-1 text-gray-900">{{ $investor->createdBy->name ?? 'System' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                            <p class="mt-1 text-gray-900">{{ $investor->updated_at->format('M d, Y H:i') }}</p>
                        </div>

                        @if($investor->investor_id_number)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Investor ID</label>
                            <p class="mt-1 text-gray-900 font-mono">{{ $investor->investor_id_number }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>