<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Add New Client</h2>
        <a href="{{ route('clients.index') }}"
            class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded mr-2">
            ← Back
        </a>
    </x-slot>

    <div class="max-w-4xl mx-auto mt-4 p-6 bg-white shadow rounded">
        <form action="{{ route('clients.store') }}" method="POST">
            @csrf

            <!-- Category: Basic Info -->
            <h3 class="text-lg font-semibold mb-2">Basic Information</h3>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block">Lead Source</label>
                    <select name="lead_source_id" class="form-select w-full">
                        <option value="">Select</option>
                        @foreach ($leadSources as $source)
                            <option value="{{ $source->id }}">{{ $source->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block">Name</label>
                    <input name="name" type="text" class="form-input w-full" />
                </div>
                <div>
                    <label class="block">Phone</label>
                    <input name="phone" type="text" class="form-input w-full" />
                </div>
                <div>
                    <label class="block">Email</label>
                    <input name="email" type="email" class="form-input w-full" />
                </div>
                <div>
                    <label class="block" for="passport_number">Passport Number *</label>
                    <input type="text"
                        name="passport_number"
                        id="passport_number"
                        value="{{ old('passport_number') }}"
                        class="form-input w-full"
                        required
                        autocomplete="off">

                    @error('passport_number')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="block">Nationality</label>
                    <input name="nationality" type="text" class="form-input w-full" />
                </div>
                <div>
                    <label class="block">Language</label>
                    <input name="language" type="text" class="form-input w-full" />
                </div>
                <div>
                    <label class="block">Resident Country</label>
                    <input name="base_location" type="text" class="form-input w-full" />
                </div>
            </div>

            <!-- Category: Investment Info -->
            <h3 class="text-lg font-semibold mb-2">Investment Details</h3>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block">User/End User</label>
                    <select name="is_investor" class="form-select w-full">
                        <option value="">Select</option>
                        <option value="1">User</option>
                        <option value="0">End User</option>
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
                    <input name="preferred_property_type" type="text" class="form-input w-full" />
                </div>
                <div>
                    <label class="block">Locations</label>
                    <input name="preferred_location" type="text" class="form-input w-full" />
                </div>
                                <div>
                    <label class="block">Investment Budget</label>
                    <input name="investment_budget" type="text" class="form-input w-full" />
                </div>
                <div>
                    <label class="block">Source of Funds</label>
                    <input name="employment_source" type="text" class="form-input w-full" />
                </div>
                <div>
                    <label class="block">Funds Location</label>
                    <input name="funds_location" type="text" class="form-input w-full" />
                </div>
                <div>
                    <label class="block">UAE Visa Required</label>
                    <select name="uae_visa_required" class="form-select w-full">
                        <option value="">Select</option>
                        <option value="1">Required</option>
                        <option value="0">Not Required</option>
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
                    <textarea name="cp_remarks" class="form-textarea w-full" rows="3"></textarea>
                </div>
            </div>

            <div class="text-right">
                <button type="submit" class="bg-[#0e2442] text-white px-4 py-2 rounded">Add Client</button>
            </div>
        </form>
    </div>
</x-app-layout>