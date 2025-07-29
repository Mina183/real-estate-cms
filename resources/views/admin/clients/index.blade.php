<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Review Partners and Clients</h2>
        <div class="mb-4">
            <a href="{{ route('dashboard') }}" class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="p-6">
        <form method="GET" class="mb-4 flex flex-wrap sm:flex-nowrap gap-4">
            <select name="channel_partner_id" class="form-select w-full sm:w-64 max-w-full">
                <option value="">All Partners</option>
                @foreach($partners as $partner)
                    <option value="{{ $partner->id }}" {{ request('channel_partner_id') == $partner->id ? 'selected' : '' }}>
                        {{ $partner->name }} ({{ $partner->email }})
                    </option>
                @endforeach
            </select>

            <select name="lead_source_id" class="form-select">
                <option value="">All Lead Sources</option>
                @foreach($leadSources as $source)
                    <option value="{{ $source->id }}" {{ request('lead_source_id') == $source->id ? 'selected' : '' }}>
                        {{ $source->name }}
                    </option>
                @endforeach
            </select>

            <input type="text" name="name" value="{{ request('name') }}" placeholder="Search client name" class="form-input">

            <button class="bg-[#0e2442] text-white px-4 py-2 rounded">Filter</button>
        </form>

        {{-- Tabbed View --}}
        <div x-data="{
            tab: '{{ request('tab', 'basic') }}',
            switchTab(newTab) {
                this.tab = newTab;
                const url = new URL(window.location.href);
                url.searchParams.set('tab', newTab);
                window.location.href = url.toString();
            }
        }">
            <div class="mb-4 border-b border-gray-400 bg-gray-200">
                <button @click="switchTab('basic')" :class="tab === 'basic' ? 'bg-[#0e2442] text-white' : 'bg-gray-200 text-gray-700'" class="px-4 py-2 rounded">Basic</button>
                <button @click="switchTab('finance')" :class="tab === 'finance' ? 'bg-[#0e2442] text-white' : 'bg-gray-200 text-gray-700'" class="px-4 py-2 rounded ml-2">Finance</button>
                <button @click="switchTab('contact')" :class="tab === 'contact' ? 'bg-[#0e2442] text-white' : 'bg-gray-200 text-gray-700'" class="px-4 py-2 rounded ml-2">Contact</button>
                <button @click="switchTab('documents')" :class="tab === 'documents' ? 'bg-[#0e2442] text-white' : 'bg-gray-200 text-gray-700'" class="px-4 py-2 rounded ml-2">Documents</button>
            </div>

            {{-- BASIC --}}
            <div x-show="tab === 'basic'" class="overflow-x-auto w-full max-w-full">
                <table class="w-full table-auto border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border px-4 py-2 text-left">Partner</th>
                            <th class="border px-4 py-2 text-left">Lead Source</th>
                            <th class="border px-4 py-2 text-left">Investor/Buyer</th>
                            <th class="border px-4 py-2 text-left">Investor Type</th>
                            <th class="border px-4 py-2 text-left ">Name</th>
                            <th class="border px-4 py-2 text-left">Passport Number</th>
                            <th class="border px-4 py-2 text-left">Phone</th>
                            <th class="border px-4 py-2 text-left">Email</th>
                            <th class="border px-4 py-2 text-left">Contact Method</th>
                            <th class="border px-4 py-2 text-left">Nationality</th>
                            <th class="border px-4 py-2 text-left">Language</th>
                            <th class="border px-4 py-2 text-left">Base Location</th>
                            <th class="border px-4 py-2 text-left">Preferred Property</th>
                            <th class="border px-4 py-2 text-left">Preferred Location</th>
                            <th class="border px-4 py-2 text-left">UAE Visa</th>
                            <th class="border px-4 py-2 text-left">CP Remarks</th>
                            <th class="border px-4 py-2 text-left">
                                <span class="font-semibold text-left">Sales Funnel Stage</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clients as $client)
                            <tr>
                                <td class="border px-4 py-2">{{ $client->channelPartner->name ?? '-' }}</td>
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
            </div>

            {{-- FINANCE --}}
            <div x-show="tab === 'finance'" x-cloak class="overflow-x-auto mt-4">
                <table class="w-full table-auto border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border px-4 py-2 text-left">Property Detail Type</th>
                            <th class="border px-4 py-2 text-left">Investment Type</th>
                            <th class="border px-4 py-2 text-left">Investment Budget</th>
                            <th class="border px-4 py-2 text-left">Employment / Source of Funds</th>
                            <th class="border px-4 py-2 text-left">Funds Location</th>
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
            </div>

            {{-- CONTACT --}}
            <div x-show="tab === 'contact'" x-cloak class="mt-4">
                @foreach($clients as $client)
                    @if($client->communications->isNotEmpty())
                        <div class="mb-4">
                            <h4 class="font-semibold text-lg">{{ $client->name }}</h4>
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
                                    @foreach($client->communications as $comm)
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
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- DOCUMENTS --}}
            <div x-show="tab === 'documents'" x-cloak class="mt-4">
                @foreach($clients as $client)
                    @if($client->documents->isNotEmpty())
                        <div class="mb-4">
                            <h4 class="font-semibold text-lg">{{ $client->name }}</h4>
                            <ul class="list-disc ml-6">
                                @foreach($client->documents as $doc)
                                    <li>
                                    <a href="{{ Storage::url($doc->path) }}" target="_blank" class="text-blue-600 underline">
                                        {{ $doc->filename }}
                                    </a>
                                        <span class="text-gray-500 text-sm">({{ $doc->created_at->format('d M Y') }})</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="mt-6">
            {{ $clients->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>