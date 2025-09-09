<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Edit Client Info</h2>
        <a href="{{ route('clients.index') }}"
            class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded mr-2">
            ← Back
        </a>
    </x-slot>

    <div class="max-w-4xl mx-auto mt-4 p-6 bg-white shadow rounded">
        <form action="{{ route('clients.update', $client) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Category: Basic Info -->
            <h3 class="text-lg font-semibold mb-2">Basic Information</h3>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block">Lead Source</label>
                    <select name="lead_source_id" class="form-select w-full">
                        <option value="">Select</option>
                        @foreach ($leadSources as $source)
                            <option value="{{ $source->id }}" {{ old('lead_source_id', $client->lead_source_id) == $source->id ? 'selected' : '' }}>
                                {{ $source->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block">Name</label>
                    <input name="name" type="text" class="form-input w-full" value="{{ old('name', $client->name) }}" />
                </div>
                <div>
                    <label class="block">Phone</label>
                    <input name="phone" type="text" class="form-input w-full" value="{{ old('phone', $client->phone) }}" />
                </div>
                <div>
                    <label class="block">Email</label>
                    <input name="email" type="email" class="form-input w-full" value="{{ old('email', $client->email) }}" />
                </div>
                <div>
                    <label class="block">Nationality</label>
                    <input name="nationality" type="text" class="form-input w-full" value="{{ old('nationality', $client->nationality) }}" />
                </div>
                <div>
                    <label class="block">Language</label>
                    <input name="language" type="text" class="form-input w-full" value="{{ old('language', $client->language) }}" />
                </div>
                <div>
                    <label class="block">Resident Country</label>
                    <input name="base_location" type="text" class="form-input w-full" value="{{ old('base_location', $client->base_location) }}" />
                </div>
            </div>

            <!-- Category: Investment Info -->
            <h3 class="text-lg font-semibold mb-2">Investment Details</h3>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block">User/End User</label>
                    <select name="is_investor" class="form-select w-full">
                        <option value="">Select</option>
                        <option value="1" {{ $client->is_investor == 1 ? 'selected' : '' }}>User</option>
                        <option value="0" {{ $client->is_investor == 0 ? 'selected' : '' }}>End User</option>
                    </select>
                </div>
                <div>
                    <label for="investor_type" class="form-label">Investor Type</label>
                    <select id="investor_type" name="investor_type" class="form-select">
                        <option value="">-- Select --</option>
                        <option value="off-plan"  {{ old('investor_type', $client->investor_type ?? '') === 'off-plan' ? 'selected' : '' }}>Off Plan</option>
                        <option value="secondary" {{ old('investor_type', $client->investor_type ?? '') === 'secondary' ? 'selected' : '' }}>Secondary</option>
                        <option value="distressed" {{ old('investor_type', $client->investor_type ?? '') === 'distressed' ? 'selected' : '' }}>Distressed</option>
                    </select>
                </div>
                <div>
                    <label class="block">Preferred Property Type (Appartment, Town House, Villa)</label>
                    <input name="preferred_property_type" type="text" class="form-input w-full" value="{{ old('preferred_property_type', $client->preferred_property_type) }}" />
                </div>
                <div>
                    <label class="block">Locations</label>
                    <input name="preferred_location" type="text" class="form-input w-full" value="{{ old('preferred_location', $client->preferred_location) }}" />
                </div>
                                <div>
                    <label class="block">Investment Budget</label>
                    <input name="investment_budget" type="text" class="form-input w-full" value="{{ old('investment_budget', $client->investment_budget) }}" />
                </div>
                <div>
                    <label class="block">Source od Funds</label>
                    <input name="employment_source" type="text" class="form-input w-full" value="{{ old('employment_source', $client->employment_source) }}" />
                </div>
                <div>
                    <label class="block">Funds Location</label>
                    <input name="funds_location" type="text" class="form-input w-full" value="{{ old('funds_location', $client->funds_location) }}" />
                </div>
                <div>
                    <label class="block">UAE Visa Required</label>
                    <select name="uae_visa_required" class="form-select w-full">
                        <option value="">Select</option>
                        <option value="1" {{ $client->uae_visa_required == 1 ? 'selected' : '' }}>Required</option>
                        <option value="0" {{ $client->uae_visa_required == 0 ? 'selected' : '' }}>Not Required</option>
                    </select>
                </div>
            </div>

            <!-- Category: CRM Notes -->
            <h3 class="text-lg font-semibold mb-2">Sales Funnel & Notes</h3>
            <div class="grid grid-cols-1 gap-4 mb-6">
                <div>
                    <label class="block">Funnel Stage</label>
                    <select name="funnel_stage" class="form-select w-full">
                    <option value="">All Funnel Stages</option>
                    @foreach($funnelStages as $key => $label)
                        <option value="{{ $key }}" {{ request('funnel_stage') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                </div>
                <div>
                    <label class="block">CP Remarks</label>
                    <textarea name="cp_remarks" class="form-textarea w-full" rows="3">{{ old('cp_remarks', $client->cp_remarks) }}</textarea>
                </div>
            </div>

            <div class="text-right">
                <button type="submit" class="bg-[#0e2442] text-white px-4 py-2 rounded">Update Client</button>
            </div>
        </form>
    </div>
</x-app-layout>