<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Clients
        </h2>
        <div class="mb-4">
        <div class="mb-4">
            <a href="{{ route('dashboard') }}" class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                ← Back to Dashboard
            </a>
        </div>
        </div>
    </x-slot>
<div class="p-2 bg-[#0e2442]">
<div class="mt-4 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<a href="{{ route('clients.create') }}"
    class="bg-gray-200 text-gray-800 px-4 py-2 rounded shadow hover:bg-[#1b3860] transition mb-4 mt-4">
    + Add New Client
</a>
</div>

<div class="mt-3 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <form method="GET" action="" class="flex items-center flex-wrap gap-4 max-w-3xl">
        <button type="submit" formaction="#" onclick="handleClientAction(event)" class="bg-gray-200 text-gray-800 px-4 py-2 rounded">
            Edit/Delete a client
        </button>
        <select name="client_id" id="clientSelect" class="select2" required>
            <option value="">-- Search client by name or email --</option>
            @foreach ($clients as $client)
                <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->email }})</option>
            @endforeach
        </select>

        <select name="action" id="actionSelect" class="border px-3 py-2 rounded" required>
            <option value="">-- Select action --</option>
            <option value="edit">Edit</option>
            <option value="delete">Delete</option>
        </select>
    </form>
</div>

<div x-data="{ filtersOpen: false }" class="mt-2 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Toggle button -->
    <button @click="filtersOpen = !filtersOpen"
        class="bg-[#0e2442] text-white border border-white px-4 py-2 rounded mb-4">
        🔍 Show Filters
    </button>

    <div x-show="filtersOpen" x-cloak>
    <form method="GET" action="{{ route('clients.index') }}" class="space-y-4">
        <details open class="border rounded p-4">
            <summary class="font-semibold cursor-pointer text-white">Basic Information</summary>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4">
                <input type="text" name="name" placeholder="Client Name" class="form-input w-full" value="{{ request('name') }}">
                <input type="text" name="email" placeholder="Email" class="form-input w-full" value="{{ request('email') }}">
                <input type="text" name="phone" placeholder="Phone" class="form-input w-full" value="{{ request('phone') }}">
                <input type="text" name="passport_number" placeholder="Passport Number" value="{{ request('passport_number') }}" class="form-input w-full">
                <input type="text" name="nationality" placeholder="Nationality" class="form-input w-full" value="{{ request('nationality') }}">
                <input type="text" name="language" placeholder="Language" class="form-input w-full" value="{{ request('language') }}">
                <input type="text" name="base_location" placeholder="Base Location" class="form-input w-full" value="{{ request('base_location') }}">
                <select name="lead_source_id" class="form-select w-full">
                    <option value="">All Lead Sources</option>
                    @foreach ($leadSources as $source)
                        <option value="{{ $source->id }}" {{ request('lead_source_id') == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                    @endforeach
                </select>
                <select name="is_investor" class="form-select w-full">
                    <option value="">Investor or Buyer?</option>
                    <option value="1" {{ request('is_investor') === '1' ? 'selected' : '' }}>Investor</option>
                    <option value="0" {{ request('is_investor') === '0' ? 'selected' : '' }}>End Buyer</option>
                </select>
                <input type="text" name="investor_type" placeholder="Investor Type" class="form-input w-full" value="{{ request('investor_type') }}">
                <input type="text" name="preferred_property_type" placeholder="Preferred Property Type" class="form-input w-full" value="{{ request('preferred_property_type') }}">
                <input type="text" name="preferred_location" placeholder="Preferred Location" class="form-input w-full" value="{{ request('preferred_location') }}">
                <select name="uae_visa_required" class="form-select w-full">
                    <option value="">UAE Visa</option>
                    <option value="1" {{ request('uae_visa_required') === '1' ? 'selected' : '' }}>Required</option>
                    <option value="0" {{ request('uae_visa_required') === '0' ? 'selected' : '' }}>Not Required</option>
                </select>
                <input type="text" name="funnel_stage" placeholder="Funnel Stage" class="form-input w-full" value="{{ request('funnel_stage') }}">
                <input type="text" name="best_contact_method" placeholder="Contact Method" class="form-input w-full" value="{{ request('best_contact_method') }}">
            </div>
        </details>

        <details class="border rounded p-4">
            <summary class="font-semibold cursor-pointer text-white">Finance</summary>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4">
                <input type="text" name="property_detail_type" placeholder="Property Detail Type" class="form-input w-full" value="{{ request('property_detail_type') }}">
                <input type="text" name="investment_type" placeholder="Investment Type" class="form-input w-full" value="{{ request('investment_type') }}">
                <input type="text" name="investment_budget" placeholder="Investment Budget" class="form-input w-full" value="{{ request('investment_budget') }}">
                <input type="text" name="employment_source" placeholder="Employment / Funds Source" class="form-input w-full" value="{{ request('employment_source') }}">
                <input type="text" name="funds_location" placeholder="Funds Location" class="form-input w-full" value="{{ request('funds_location') }}">
            </div>
        </details>

        <div class="text-left">
            <button type="submit" class="bg-[#0e2442] text-white border border-white px-4 py-2 rounded">Apply Filters</button>
        </div>
    </form>
    </div>
</div>

<div class="mb-4 mt-4 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <a href="{{ route('clients.index') }}" class="bg-[#0e2442] text-white border border-white px-4 py-2 rounded hover:bg-gray-300">
        Clear Filters
    </a>
</div>
</div>
    <div class="p-6 overflow-x-auto">
        <div x-data="{
                        tab: '{{ request('tab', 'basic') }}',
                        switchTab(newTab) {
                            this.tab = newTab;

                            const url = new URL(window.location.href);

                            url.searchParams.set('tab', newTab);

                            // Clear `client_id` ONLY if switching away from 'contact'
                            if (newTab !== 'contact') {
                                url.searchParams.delete('client_id');
                            }

                            // Keep all other filters in the URL
                            window.location.href = url.toString(); // full redirect to apply filters
                        }
                    }">
            <div class="mb-4 border-b border-gray-400 bg-gray-200 min-w-full">
                <button
                    @click="switchTab('basic')"
                        :class="tab === 'basic' ? 'bg-[#0e2442] text-white' : 'bg-gray-200 text-gray-700'"
                        class="px-4 py-2 rounded">
                        Basic
                    </button>
                    <button
                        @click="switchTab('finance')"
                        :class="tab === 'finance' ? 'bg-[#0e2442] text-white' : 'bg-gray-200 text-gray-700'"
                        class="px-4 py-2 rounded ml-2">
                        Finance
                    </button>
                    <button
                        @click="switchTab('contact')"
                        :class="tab === 'contact' ? 'bg-[#0e2442] text-white' : 'bg-gray-200 text-gray-700'"
                        class="px-4 py-2 rounded ml-2">
                        Contact
                    </button>
                    <button
                        @click="switchTab('documents')"
                        :class="tab === 'documents' ? 'bg-[#0e2442] text-white' : 'bg-gray-200 text-gray-700'"
                        class="px-4 py-2 rounded ml-2">
                        Documents
                    </button>
            </div>

        <div x-show="tab === 'basic'" class="overflow-x-auto w-full max-w-full">
            <h3 class="font-semibold text-lg mb-2">Basic Information</h3>
            <table class="min-w-full table-auto border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border px-4 py-2">Lead Source</th>
                        <th class="border px-4 py-2">Investor/Buyer</th>
                        <th class="border px-4 py-2">Investor Type</th>
                        <th class="border px-4 py-2">Name</th>
                        <th class="border px-4 py-2">Passport Number</th>
                        <th class="border px-4 py-2">Phone</th>
                        <th class="border px-4 py-2">Email</th>
                        <th class="border px-4 py-2">Contact Method</th>
                        <th class="border px-4 py-2">Nationality</th>
                        <th class="border px-4 py-2">Language</th>
                        <th class="border px-4 py-2">Base Location</th>
                        <th class="border px-4 py-2">Preferred Property</th>
                        <th class="border px-4 py-2">Preferred Location</th>
                        <th class="border px-4 py-2">UAE Visa</th>
                        <th class="border px-4 py-2">CP Remarks</th>
                        <th class="border px-4 py-2">
                            <span class="font-semibold">Sales Funnel Stage</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clients as $client)
                        <tr>
                            <td class="border px-4 py-2">{{ $client->leadSource->name ?? '-' }}</td>
                            <td class="border px-4 py-2">{{ $client->is_investor ? 'Investor' : 'End Buyer' }}</td>
                            <td class="border px-4 py-2">{{ $client->investor_type ?? '-' }}</td>
                            <td class="border px-4 py-2">{{ $client->name }}</td>
                            <td class="border px-4 py-2">{{ $client->passport_number }}</td>
                            <td class="border px-4 py-2">{{ $client->phone }}</td>
                            <td class="border px-4 py-2">{{ $client->email }}</td>
                            <td class="border px-4 py-2">{{ $client->best_contact_method }}</td>
                            <td class="border px-4 py-2">{{ $client->nationality }}</td>
                            <td class="border px-4 py-2">{{ $client->language }}</td>
                            <td class="border px-4 py-2">{{ $client->base_location }}</td>
                            <td class="border px-4 py-2">{{ $client->preferred_property_type }}</td>
                            <td class="border px-4 py-2">{{ $client->preferred_location }}</td>
                            <td class="border px-4 py-2">{{ $client->uae_visa_required ? 'Required' : 'Not Required' }}</td>
                            <td class="border px-4 py-2">{{ $client->cp_remarks }}</td>
                            <td class="border px-4 py-2">{{ $client->funnel_stage }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $clients->withQueryString()->links() }}
        </div>

        <div x-show="tab === 'finance'" x-cloak>
            <h3 class="font-semibold text-lg mb-2">Financial & Property Context</h3>
            <table class="w-full table-auto border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border px-4 py-2">Property Detail Type</th>
                        <th class="border px-4 py-2">Investment Type</th>
                        <th class="border px-4 py-2">Investment Budget</th>
                        <th class="border px-4 py-2">Employment / Source of Funds</th>
                        <th class="border px-4 py-2">Funds Location</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clients as $client)
                        <tr>
                            <td class="border px-4 py-2">{{ $client->property_detail_type }}</td>
                            <td class="border px-4 py-2">{{ $client->investment_type }}</td>
                            <td class="border px-4 py-2">{{ $client->investment_budget }}</td>
                            <td class="border px-4 py-2">{{ $client->employment_source }}</td>
                            <td class="border px-4 py-2">{{ $client->funds_location }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $clients->withQueryString()->links() }}
        </div>

<div x-show="tab === 'contact'" x-cloak>
    <h3 class="font-semibold text-lg mb-4">Communication Log</h3>

    {{-- Filter form --}}
    <form method="GET" action="{{ route('clients.index') }}" class="mb-4">
        <input type="hidden" name="tab" value="contact">

        <div>
        <label for="client_filter" class="block mb-1 font-medium">Select Client to add new note</label>
        <select id="client_filter" name="client_id" onchange="this.form.submit()" class="form-select w-full max-w-sm">
            <option value="">-- Show All --</option>
            @foreach ($clients as $clientOption)
                <option value="{{ $clientOption->id }}" {{ request('client_id') == $clientOption->id ? 'selected' : '' }}>
                    {{ $clientOption->name }}
                </option>
            @endforeach
        </select>
        </div>
        @if (request('client_id'))
            <div class="mt-6">
                <a href="{{ route('clients.index', ['tab' => 'contact']) }}"
                class="inline-block bg-gray-300 hover:bg-gray-400 text-black text-sm px-3 py-1 rounded">
                    Clear Filter
                </a>
            </div>
        @endif
    </form>

        @php
            $selectedClientId = request('client_id');
        @endphp

        {{-- Case: Specific client selected --}}
        @if ($selectedClientId)
            @php
                $selectedClient = $clients->firstWhere('id', $selectedClientId);
            @endphp

            @if ($selectedClient)
                <div class="mb-6 border-b pb-4">
                    <h4 class="font-semibold mb-2 bg-[#0e2442] text-white pl-2">{{ $selectedClient->name }}</h4>

                    {{-- Note form only for selected client --}}
                    <form action="{{ route('clients.communications.store', $selectedClient->id) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="grid grid-cols-3 gap-4 mb-2">
                            <input type="date" name="date" class="form-input w-full" placeholder="Date">
                            <input type="text" name="action" class="form-input w-full" placeholder="Action">
                            <input type="text" name="update" class="form-input w-full" placeholder="Update">
                            <input type="text" name="feedback" class="form-input w-full" placeholder="Feedback">
                            <input type="text" name="outcome" class="form-input w-full" placeholder="Outcome">
                        </div>
                        <button type="submit" class="bg-[#0e2442] text-white px-4 py-2 rounded">Save Note</button>
                    </form>

                    {{-- Note history for selected client --}}
                    <table class="w-full table-auto border-collapse text-sm mb-2">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border px-4 py-2 text-left">Date</th>
                                <th class="border px-4 py-2 text-left">Action</th>
                                <th class="border px-4 py-2 text-left">Feedback</th>
                                <th class="border px-4 py-2 text-left">Outcome</th>
                                <th class="border px-4 py-2 text-left">Last Update</th>
                                <th class="border px-4 py-2 text-left">Edit Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($selectedClient->communications as $comm)
                                <tr>
                                    <td class="border px-4 py-2">{{ $comm->date }}</td>
                                    <td class="border px-4 py-2">{{ $comm->action }}</td>
                                    <td class="border px-4 py-2">{{ $comm->feedback }}</td>
                                    <td class="border px-4 py-2">{{ $comm->outcome }}</td>
                                    <td class="border px-4 py-2">{{ $comm->update }}</td>
                                    <td class="border px-4 py-2"><a href="{{ route('clients.communications.edit', $comm->id) }}"
                                    class="text-blue-600 hover:underline text-sm">✏️ Edit</a></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-gray-500 py-2">No notes yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $client->paginatedCommunications->withQueryString()->links() }}
                </div>
            @endif

        {{-- Case: No client selected — show all clients' history, no form --}}
        @else
            @foreach ($clients as $client)
                @if ($client->communications->isNotEmpty())
                    <div class="mb-6 border-b pb-4">
                        <h4 class="font-semibold mb-2 bg-[#0e2442] text-white pl-2">{{ $client->name }}</h4>

                        <table class="w-full table-auto border-collapse text-sm mb-2">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="border px-4 py-2 text-left">Date</th>
                                    <th class="border px-4 py-2 text-left">Action</th>
                                    <th class="border px-4 py-2 text-left">Feedback</th>
                                    <th class="border px-4 py-2 text-left">Outcome</th>
                                    <th class="border px-4 py-2 text-left">Last Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($client->paginatedCommunications as $comm)
                                    <tr>
                                        <td class="border px-4 py-2">{{ $comm->date }}</td>
                                        <td class="border px-4 py-2">{{ $comm->action }}</td>
                                        <td class="border px-4 py-2">{{ $comm->feedback }}</td>
                                        <td class="border px-4 py-2">{{ $comm->outcome }}</td>
                                        <td class="border px-4 py-2">{{ $comm->update }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $client->paginatedCommunications->withQueryString()->links() }}
                    </div>
                @endif
            @endforeach
        @endif
    </div>

       <div x-show="tab === 'documents'" x-cloak>
            <h3 class="font-semibold text-lg mb-4">Uploaded Documents</h3>

            {{-- Client filter shown only on this tab --}}
            <form method="GET" action="{{ route('clients.index') }}" class="mb-4">
                <input type="hidden" name="tab" value="documents">

                <div>
                <label for="client_filter_documents" class="block mb-1 font-medium">Select Client to upload new document</label>
                <select id="client_filter_documents" name="client_id" onchange="this.form.submit()" class="form-select w-full max-w-sm">
                    <option value="">-- Show All --</option>
                    @foreach ($clients as $clientOption)
                        <option value="{{ $clientOption->id }}" {{ request('client_id') == $clientOption->id ? 'selected' : '' }}>
                            {{ $clientOption->name }}
                        </option>
                    @endforeach
                </select>
                </div>
                    @if (request('client_id'))
                        <div class="mt-6">
                            <a href="{{ route('clients.index', ['tab' => 'documents']) }}"
                            class="inline-block bg-gray-300 hover:bg-gray-400 text-black text-sm px-3 py-1 rounded">
                                Clear Filter
                            </a>
                        </div>
                    @endif
            </form>

            @php
                $selectedClientId = request('client_id');
                $filteredClients = $selectedClientId
                    ? $clients->where('id', $selectedClientId)
                    : $clients;

                $hasAnyDocuments = false;
            @endphp

            @foreach ($filteredClients as $client)
                @if ($client->documents->count() > 0 || $selectedClientId == $client->id)
                    @php $hasAnyDocuments = true; @endphp

                    <div class="mb-6">
                        <h4 class="font-semibold mb-2 bg-[#0e2442] text-white pl-2">{{ $client->name }}</h4>

                        {{-- Upload form only when one client is selected --}}
                        @if ($selectedClientId == $client->id)
                            <form action="{{ route('clients.documents.store', $client->id) }}" method="POST" enctype="multipart/form-data" class="mb-4">
                                @csrf
                                <div class="flex items-center gap-2 mb-4">
                                    <input type="file" name="document" required class="block w-full text-sm border rounded">
                                    <button type="submit" class="bg-[#0e2442] text-white px-3 py-1 rounded text-sm">Upload</button>
                                </div>
                            </form>
                        @endif

                        {{-- Document table --}}
                        <table class="w-full table-fixed border-collapse text-sm mb-2">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="border px-4 py-2 text-left">Filename</th>
                                    <th class="border px-4 py-2 text-left">Uploaded At</th>
                                    <th class="border px-4 py-2 text-left">Download</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($client->paginatedDocuments as $doc)
                                    <tr>
                                        <td class="border px-4 py-2">{{ $doc->filename }}</td>
                                        <td class="border px-4 py-2">{{ $doc->created_at->format('d M Y') }}</td>
                                        <td class="border px-4 py-2">
                                           <a href="{{ Storage::disk('private')->temporaryUrl($doc->path, now()->addMinutes(10)) }}" class="text-blue-600 underline" target="_blank">Download</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-gray-500 py-2">No documents uploaded.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $client->paginatedDocuments->withQueryString()->links() }}
                    </div>
                @endif
            @endforeach

            {{-- Show if no documents exist at all --}}
            @if (! $hasAnyDocuments && ! $clients->isEmpty())
                <div class="text-gray-500 italic">No documents uploaded yet.</div>
            @endif

            {{-- Fallback if no clients exist at all --}}
            @if ($clients->isEmpty())
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-400">[No clients yet]</h4>
                    <form>
                        <div class="flex items-center gap-2 mb-4 opacity-80 pointer-events-none">
                            <input type="file" name="document" class="block w-full text-sm border rounded" disabled>
                            <button type="button" class="bg-gray-500 text-white px-3 py-1 rounded text-sm cursor-not-allowed">Upload</button>
                        </div>
                    </form>
                    <table class="w-full table-fixed border-collapse text-sm mb-2">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border px-4 py-2 text-left">Filename</th>
                                <th class="border px-4 py-2 text-left">Uploaded At</th>
                                <th class="border px-4 py-2 text-left">Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3" class="text-center text-gray-300 py-2">...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        </div>
        </div>
        </div>
        </div>
        </div>
        </div>
    </div>
</x-app-layout>

<script>
    function handleClientAction(event) {
        event.preventDefault();

        const clientId = document.getElementById('clientSelect').value;
        const action = document.getElementById('actionSelect').value;

        if (!clientId || !action) {
            alert('Please select a client and an action.');
            return;
        }

        if (action === 'edit') {
            window.location.href = `/clients/${clientId}/edit`;
        } else if (action === 'delete') {
            if (confirm('Are you sure you want to delete this client?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/clients/${clientId}`;
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    }

        $(document).ready(function() {
        $('#clientSelect').select2();
    });
</script>