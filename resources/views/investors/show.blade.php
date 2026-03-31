<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $investor->organization_name ?? $investor->legal_entity_name ?? 'Investor Details' }}
            </h2>
            <div class="flex space-x-2">
                @can('changeStage', $investor)
                    <a href="{{ route('investors.change-stage.form', $investor) }}" 
                       class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        🔄 Change Stage
                    </a>
                @endcan

                @can('view', $investor)
                    <a href="{{ route('investors.activity', $investor) }}" 
                        class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                            📋 Activity Log
                    </a>
                @endcan
                
                <div class="flex space-x-2">
                    @can('update', $investor)
                        <a href="{{ route('investors.edit', $investor) }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit
                        </a>
                    @endcan

                    @can('update', $investor)
                        <a href="{{ route('email-drafts.create', ['investor_id' => $investor->id]) }}" 
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            ✉️ Compose Email
                        </a>
                    @endcan

                    @can('update', $investor)
                    @php
                        $portalAllowed = in_array($investor->stage, ['ppm_issued', 'kyc_in_progress', 'subscription_signed', 'approved', 'funded', 'active']);
                        $portalExists = $investor->investorUser !== null;
                    @endphp

                    @if($portalExists)
                        <span class="bg-gray-300 text-gray-600 font-bold py-2 px-4 rounded cursor-not-allowed">
                            ✅ Portal Active
                        </span>
                    @elseif($portalAllowed)
                        <form method="POST" action="{{ route('investors.create-portal-access', $investor) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded"
                                    onclick="return confirm('Create portal access and send credentials to primary contact?')">
                                🔐 Create Portal Access
                            </button>
                        </form>
                    @else
                        <span class="bg-gray-200 text-gray-400 font-bold py-2 px-4 rounded cursor-not-allowed" 
                            title="Available from PPM Issued stage">
                            🔐 Portal Access
                        </span>
                    @endif
                    @endcan

                    <a href="{{ route('investors.index') }}" 
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        ← Back
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            {{-- TAB NAVIGATION --}}
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-6" id="tabs">
                    <button onclick="switchTab('overview')" id="tab-overview"
                        class="tab-btn border-b-2 border-blue-500 text-blue-600 py-3 px-1 text-sm font-medium">
                        Overview
                    </button>
                    <button onclick="switchTab('financial')" id="tab-financial"
                        class="tab-btn border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-3 px-1 text-sm font-medium">
                        Financial
                    </button>
                    <button onclick="switchTab('contacts')" id="tab-contacts"
                        class="tab-btn border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-3 px-1 text-sm font-medium">
                        Registered Contacts
                    </button>
                    <button onclick="switchTab('meetings')" id="tab-meetings"
                        class="tab-btn border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-3 px-1 text-sm font-medium">
                        Meetings
                    </button>
                    <button onclick="switchTab('communications')" id="tab-communications"
                        class="tab-btn border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-3 px-1 text-sm font-medium">
                        Communications
                    </button>
                    <button onclick="switchTab('system')" id="tab-system"
                        class="tab-btn border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-3 px-1 text-sm font-medium">
                        System
                    </button>

                    @can('update', $investor)
                    <button onclick="switchTab('doc-links')" id="tab-doc-links"
                        class="tab-btn border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-3 px-1 text-sm font-medium">
                        Document Links
                        @php $pendingCount = $investor->documentAccessLinks->flatMap->accessRequests->where('status','pending')->count(); @endphp
                        @if($pendingCount > 0)
                            <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ $pendingCount }}</span>
                        @endif
                    </button>
                    @endcan
                </nav>
            </div>

            {{-- OVERVIEW TAB --}}
            <div id="pane-overview" class="tab-pane space-y-6">

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

            </div>

            {{-- FINANCIAL TAB --}}
            <div id="pane-financial" class="tab-pane hidden space-y-6">
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
            </div>

            {{-- REGISTERED CONTACTS TAB --}}
            <div id="pane-contacts" class="tab-pane hidden">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Registered Contacts</h3>

                        @if($investor->contacts->count() > 0)
                            <div class="space-y-3 mb-6">
                                @foreach($investor->contacts as $contact)
                                    <div class="border border-gray-200 rounded p-4">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-semibold text-gray-900">
                                                    {{ $contact->title }} {{ $contact->full_name }}
                                                    @if($contact->is_primary)
                                                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Primary</span>
                                                    @endif
                                                </p>
                                                <p class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $contact->role)) }}</p>
                                                @if($contact->email)
                                                    <p class="text-sm text-gray-600">{{ $contact->email }}</p>
                                                @endif
                                                @if($contact->phone)
                                                    <p class="text-sm text-gray-600">{{ $contact->phone }}</p>
                                                @endif
                                            </div>
                                            @can('update', $investor)
                                                <form method="POST" action="{{ route('investors.contacts.destroy', [$investor, $contact]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm" 
                                                            onclick="return confirm('Remove this contact?')">
                                                        Remove
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 mb-6">No contacts added yet.</p>
                        @endif

                        @can('update', $investor)
                        <div class="border-t pt-4">
                            <h4 class="text-md font-semibold text-gray-700 mb-3">Add Contact</h4>
                            <form method="POST" action="{{ route('investors.contacts.store', $investor) }}">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                        <select name="title" class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                            <option value="">--</option>
                                            <option value="Mr.">Mr.</option>
                                            <option value="Mrs.">Mrs.</option>
                                            <option value="Ms.">Ms.</option>
                                            <option value="Dr.">Dr.</option>
                                            <option value="Prof.">Prof.</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                        <select name="role" class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                            <option value="primary_contact">Primary Contact</option>
                                            <option value="legal_counsel">Legal Counsel</option>
                                            <option value="financial_officer">Financial Officer</option>
                                            <option value="authorized_signatory">Authorized Signatory</option>
                                            <option value="compliance_officer">Compliance Officer</option>
                                            <option value="beneficial_owner">Beneficial Owner</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                                        <input type="text" name="first_name" required class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                                        <input type="text" name="last_name" required class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input type="email" name="email" class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                        <input type="text" name="phone" class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="is_primary" value="1" class="mr-2">
                                            <span class="text-sm text-gray-700">Set as Primary Contact</span>
                                        </label>
                                    </div>
                                    <div class="md:col-span-2 flex space-x-4 text-sm text-gray-700">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="can_sign_documents" value="1" class="mr-2">
                                            Can Sign Documents
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="receives_capital_calls" value="1" class="mr-2">
                                            Receives Capital Calls
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="receives_distributions" value="1" class="mr-2">
                                            Receives Distributions
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="receives_reports" value="1" class="mr-2">
                                            Receives Reports
                                        </label>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded text-sm">
                                        Add Contact
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>

            {{-- MEETINGS TAB --}}
            <div id="pane-meetings" class="tab-pane hidden">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Meetings</h3>

                        @if($investor->meetings->count() > 0)
                            <div class="space-y-3 mb-6">
                                @foreach($investor->meetings as $meeting)
                                    <div class="border border-gray-200 rounded p-4">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ $meeting->meeting_date->format('M d, Y') }}
                                                </p>
                                                <p class="text-sm text-gray-600 mt-1">
                                                    <span class="font-medium">Attendees:</span> {{ $meeting->attendees }}
                                                </p>
                                                @if($meeting->outcome)
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        <span class="font-medium">Outcome:</span> {{ $meeting->outcome }}
                                                    </p>
                                                @endif
                                                <p class="text-xs text-gray-400 mt-2">
                                                    Logged by {{ $meeting->createdBy->name ?? '—' }} on {{ $meeting->created_at->format('M d, Y') }}
                                                </p>
                                            </div>
                                            @can('update', $investor)
                                                <form method="POST" action="{{ route('investors.meetings.destroy', [$investor, $meeting]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm ml-4"
                                                            onclick="return confirm('Remove this meeting log?')">
                                                        Remove
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 mb-6">No meetings logged yet.</p>
                        @endif

                        @can('update', $investor)
                        <div class="border-t pt-4">
                            <h4 class="text-md font-semibold text-gray-700 mb-3">Log Meeting</h4>
                            <form method="POST" action="{{ route('investors.meetings.store', $investor) }}">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Meeting Date *</label>
                                        <input type="date" name="meeting_date" required
                                               class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Attendees *</label>
                                        <input type="text" name="attendees" required
                                               placeholder="e.g. John Smith, Jane Doe"
                                               class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Outcome / Next Steps</label>
                                        <textarea name="outcome" rows="3"
                                                  placeholder="Summary of meeting outcome and agreed next steps..."
                                                  class="block w-full border-gray-300 rounded-md shadow-sm text-sm"></textarea>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                        Log Meeting
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>

{{-- COMMUNICATIONS TAB --}}
<div id="pane-communications" class="tab-pane hidden space-y-6">

    {{-- DRAFTS SECTION --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Email Drafts</h3>
                @can('update', $investor)
                    <a href="{{ route('email-drafts.create', ['investor_id' => $investor->id]) }}"
                       class="bg-blue-500 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded">
                        + Compose Email
                    </a>
                @endcan
            </div>

            @if($drafts->count() > 0)
                <div class="space-y-3">
                    @foreach($drafts as $draft)
                    <div class="border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $draft->subject }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                Created by {{ $draft->createdBy->name ?? '—' }} on {{ $draft->created_at->format('M d, Y H:i') }}
                                @if($draft->status === 'approved' && $draft->approvedBy)
                                    · Approved by {{ $draft->approvedBy->name }} on {{ $draft->approved_at->format('M d, Y H:i') }}
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center space-x-3 ml-4">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                @if($draft->status === 'draft') bg-gray-100 text-gray-700
                                @elseif($draft->status === 'pending_approval') bg-yellow-100 text-yellow-800
                                @elseif($draft->status === 'approved') bg-green-100 text-green-800
                                @endif">
                                @if($draft->status === 'draft') Draft
                                @elseif($draft->status === 'pending_approval') Pending Approval
                                @elseif($draft->status === 'approved') ✓ Approved
                                @endif
                            </span>
                            @if($draft->status === 'draft' || $draft->status === 'pending_approval')
                                <a href="{{ route('email-drafts.edit', $draft) }}"
                                   class="text-blue-600 hover:text-blue-800 text-xs font-medium">Edit</a>
                            @endif
                            @if($draft->status === 'approved' && $draft->created_by_user_id === auth()->id())
                                <form method="POST" action="{{ route('email-drafts.send', $draft) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="bg-teal-500 hover:bg-teal-700 text-white text-xs font-bold py-1 px-3 rounded"
                                            onclick="return confirm('Send this email now?')">
                                        Send
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">No drafts yet.</p>
            @endif
        </div>
    </div>

    {{-- SENT EMAILS SECTION --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sent Emails</h3>

            @if($emailLogs->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date <span class="normal-case font-normal">(GST)</span></th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Template</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Document</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sent By</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acknowledgement</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($emailLogs as $log)
                            <tr>
                                <td class="px-4 py-3 text-gray-900 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($log->sent_at)->timezone('Asia/Dubai')->format('M d, Y H:i') }} <span class="text-xs text-gray-400">GST</span>
                                </td>
                                <td class="px-4 py-3 text-gray-900">{{ $log->email_subject }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ ucfirst(str_replace('_', ' ', $log->template)) }}</td>
                                <td class="px-4 py-3 text-gray-500">
                                    @if($log->document_name)
                                        {{ $log->document_name }}
                                        @if($log->document_version)
                                            <span class="text-xs text-gray-400">v{{ $log->document_version }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ $log->sentBy->name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if(!$log->requires_acknowledgement)
                                        <span class="text-gray-400 text-xs">Not required</span>
                                    @elseif($log->acknowledged_at)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            ✓ {{ \Carbon\Carbon::parse($log->acknowledged_at)->timezone('Asia/Dubai')->format('M d, Y H:i') }} <span class="text-xs text-gray-400">GST</span>
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500">No emails sent yet.</p>
            @endif
        </div>
    </div>

</div>

            {{-- SYSTEM TAB --}}
            <div id="pane-system" class="tab-pane hidden space-y-6">

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

            {{-- DOCUMENT LINKS TAB --}}
            @can('update', $investor)
            <div id="pane-doc-links" class="tab-pane hidden space-y-6">

                {{-- Existing links --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Generated Links</h3>

                        @if(session('success') && request()->is('*investors*'))
                            {{-- success banner already shown above --}}
                        @endif

                        @if($investor->documentAccessLinks->isEmpty())
                            <p class="text-sm text-gray-500">No links generated yet. Use the form below to create one.</p>
                        @else
                            <div class="space-y-4">
                                @foreach($investor->documentAccessLinks as $link)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ $link->label ?: $link->package->name }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-0.5">
                                                    Package: {{ $link->package->name }}
                                                    @if(Str::startsWith($link->package->name, '[Custom]'))
                                                        <span class="ml-1 px-1.5 py-0.5 rounded text-xs bg-purple-100 text-purple-700">custom</span>
                                                    @endif
                                                    &bull; Created by {{ $link->createdBy->name ?? '—' }} on {{ $link->created_at->format('d M Y') }}
                                                </p>
                                            </div>
                                            <form action="{{ route('document-access-links.destroy', $link) }}" method="POST"
                                                  onsubmit="return confirm('Delete this link and all its access requests?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                                            </form>
                                        </div>

                                        <div class="mt-3 flex items-center space-x-2">
                                            <input type="text" readonly
                                                   value="{{ $link->public_url }}"
                                                   onclick="this.select()"
                                                   class="flex-1 text-xs border-gray-300 rounded bg-gray-50 px-2 py-1 focus:outline-none">
                                            <button type="button"
                                                    onclick="navigator.clipboard.writeText('{{ $link->public_url }}').then(() => this.textContent = 'Copied!').catch(() => {}); setTimeout(() => this.textContent = 'Copy', 1500)"
                                                    class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded border border-gray-300 whitespace-nowrap">
                                                Copy
                                            </button>
                                        </div>

                                        @php
                                            $pending  = $link->accessRequests->where('status', 'pending')->count();
                                            $approved = $link->accessRequests->where('status', 'approved')->count();
                                            $total    = $link->accessRequests->count();
                                        @endphp
                                        @if($total > 0)
                                            <div class="mt-2 flex items-center space-x-3 text-xs text-gray-500">
                                                <span>{{ $total }} request{{ $total !== 1 ? 's' : '' }}</span>
                                                @if($pending > 0)
                                                    <span class="px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800 font-medium">{{ $pending }} pending</span>
                                                @endif
                                                @if($approved > 0)
                                                    <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-800 font-medium">{{ $approved }} approved</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Generate new link --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Generate New Link</h3>

                        @if($errors->any() && old('_tab') === 'doc-links')
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                                <ul class="list-disc list-inside text-sm">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('document-access-links.store') }}" method="POST" id="doc-link-form">
                            @csrf
                            <input type="hidden" name="investor_id" value="{{ $investor->id }}">
                            <input type="hidden" name="_tab" value="doc-links">

                            <div class="space-y-4">

                                {{-- Label --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Label <span class="text-gray-400 font-normal">(optional)</span>
                                    </label>
                                    <input type="text" name="label"
                                           value="{{ old('label') }}"
                                           placeholder="e.g. Q1 2024 Pack — sent 31 Mar"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>

                                {{-- Mode selector --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Package</label>
                                    <div class="flex space-x-6 mb-3">
                                        <label class="flex items-center text-sm cursor-pointer">
                                            <input type="radio" name="_link_mode" value="existing" checked
                                                   onchange="document.getElementById('mode-existing').classList.remove('hidden'); document.getElementById('mode-custom').classList.add('hidden')"
                                                   class="mr-2 text-blue-600">
                                            Use existing package
                                        </label>
                                        <label class="flex items-center text-sm cursor-pointer">
                                            <input type="radio" name="_link_mode" value="custom"
                                                   onchange="document.getElementById('mode-existing').classList.add('hidden'); document.getElementById('mode-custom').classList.remove('hidden')"
                                                   class="mr-2 text-blue-600">
                                            Custom — select documents
                                        </label>
                                    </div>

                                    {{-- Existing package mode --}}
                                    <div id="mode-existing">
                                        @if($availablePackages->isEmpty())
                                            <p class="text-sm text-gray-500">
                                                No packages yet.
                                                @can('manage-settings')
                                                    <a href="{{ route('document-packages.create') }}" class="text-blue-600 underline">Create one in Settings.</a>
                                                @endcan
                                            </p>
                                        @else
                                            <select name="document_package_id"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                <option value="">Select a package…</option>
                                                @foreach($availablePackages as $pkg)
                                                    <option value="{{ $pkg->id }}" {{ old('document_package_id') == $pkg->id ? 'selected' : '' }}>
                                                        {{ $pkg->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>

                                    {{-- Custom document mode --}}
                                    <div id="mode-custom" class="hidden">
                                        @if($availableDocuments->isEmpty())
                                            <p class="text-sm text-gray-500">No approved documents available in the Data Room.</p>
                                        @else
                                            <div class="border border-gray-300 rounded-md divide-y divide-gray-100 max-h-64 overflow-y-auto">
                                                @foreach($availableDocuments->groupBy(fn($d) => $d->folder->folder_name ?? 'Uncategorised') as $folderName => $docs)
                                                    <div class="px-3 py-1.5 bg-gray-50">
                                                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $folderName }}</span>
                                                    </div>
                                                    @foreach($docs as $doc)
                                                        <label class="flex items-center px-3 py-2 hover:bg-gray-50 cursor-pointer">
                                                            <input type="checkbox" name="document_ids[]" value="{{ $doc->id }}"
                                                                   {{ in_array($doc->id, old('document_ids', [])) ? 'checked' : '' }}
                                                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-200">
                                                            <span class="ml-2 text-sm text-gray-700">{{ $doc->document_name }}</span>
                                                            @if($doc->file_type)
                                                                <span class="ml-1.5 text-xs text-gray-400 uppercase">{{ $doc->file_type }}</span>
                                                            @endif
                                                        </label>
                                                    @endforeach
                                                @endforeach
                                            </div>
                                            <p class="mt-1 text-xs text-gray-400">A package will be auto-created with the selected documents.</p>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <button type="submit"
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                        Generate Link
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

            </div>
            @endcan

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
        }

        // Restore tab from URL hash
        const hash = window.location.hash.replace('#', '');
        const validTabs = ['overview', 'financial', 'contacts', 'meetings', 'communications', 'system', 'doc-links'];
        if (hash && validTabs.includes(hash)) {
            switchTab(hash);
        }

        // Update URL hash on tab switch
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tab = this.id.replace('tab-', '');
                history.replaceState(null, '', '#' + tab);
            });
        });
    </script>

</x-app-layout>