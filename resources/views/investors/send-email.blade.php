<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Send Email
                @if($investor)
                    – {{ $investor->organization_name ?? $investor->legal_entity_name }}
                @endif
            </h2>
            <a href="{{ $investor ? route('investors.show', $investor) : route('investors.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST"
                  action="{{ $investor ? route('investors.send-email') : route('email-drafts.store') }}"
                  id="sendForm">
                @csrf

                @if($investor)
                    <input type="hidden" name="investor_id" value="{{ $investor->id }}">
                    <input type="hidden" name="recipient_type" value="single">
                @endif

                <!-- Recipient Selection (bulk only) -->
                @if(!$investor)
                <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recipients</h3>

                    {{-- Filter controls — JS navigation (cannot nest forms in HTML) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Send To</label>
                            <select id="recipient_type" onchange="toggleStage(this.value)"
                                    class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="all" {{ $recipients === 'all' ? 'selected' : '' }}>All Investors</option>
                                <option value="stage" {{ $recipients === 'stage' ? 'selected' : '' }}>By Stage</option>
                            </select>
                        </div>
                        <div id="stage_select" class="{{ $recipients === 'stage' ? '' : 'invisible' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stage</label>
                            <select id="stage_filter" onchange="applyFilters()"
                                    class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="">-- All stages --</option>
                                @foreach($stages as $s)
                                    <option value="{{ $s }}" {{ ($selectedStage ?? '') === $s ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', ucfirst($s)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end pb-0.5">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" id="assigned_to_me" onchange="applyFilters()"
                                       {{ ($assignedToMe ?? false) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                                <span class="text-sm font-medium text-gray-700">Only my assigned investors</span>
                            </label>
                        </div>
                    </div>

                    {{-- Recipient list --}}
                    <div class="mt-5">
                        @if($investors->count() > 0)
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-semibold text-gray-700">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 mr-1">{{ $investors->count() }}</span>
                                    investor(s) will receive this email
                                </p>
                            </div>
                            <div class="border border-gray-200 rounded-lg overflow-hidden max-h-56 overflow-y-auto">
                                <table class="min-w-full divide-y divide-gray-100 text-sm">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Investor</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Stage</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach($investors as $inv)
                                            @php $contact = $inv->contacts->where('is_primary', true)->first() ?? $inv->contacts->first(); @endphp
                                            <tr class="{{ $contact ? '' : 'opacity-40' }}">
                                                <td class="px-4 py-2 font-medium text-gray-800">{{ $inv->organization_name ?? $inv->legal_entity_name }}</td>
                                                <td class="px-4 py-2 text-gray-500">
                                                    @if($contact)
                                                        {{ $contact->email }}
                                                    @else
                                                        <span class="text-xs text-red-400 italic">No contact email</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2">
                                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                                        {{ str_replace('_', ' ', ucfirst($inv->stage)) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-sm text-yellow-700">No investors match the selected filters.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Pass filter params to the POST send form --}}
                    <input type="hidden" name="recipient_type" value="{{ $recipients }}" form="sendForm">
                    <input type="hidden" name="stage" value="{{ $selectedStage ?? '' }}" form="sendForm">
                    @if($assignedToMe ?? false)
                        <input type="hidden" name="assigned_to_me" value="1" form="sendForm">
                    @endif
                </div>
                @endif

        <!-- Template Selection -->
        <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Email Template</h3>
            
            <div class="space-y-3">
                @foreach($templates as $key => $template)
                <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="template" value="{{ $key }}" class="mt-1 mr-3">
                    <div>
                        <p class="font-medium text-gray-900">{{ $template['name'] }}</p>
                        <p class="text-sm text-gray-500">Subject: {{ $template['subject'] }}</p>
                        <p class="text-xs text-gray-400">Stages: {{ implode(', ', $template['stages']) }}</p>
                    </div>
                </label>
                @endforeach

                <!-- Custom Template -->
                <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="template" value="custom" class="mt-1 mr-3" id="custom_radio">
                    <div>
                        <p class="font-medium text-gray-900">Custom Email</p>
                        <p class="text-sm text-gray-500">Write your own email subject and body</p>
                    </div>
                </label>

                <div id="custom_fields" class="hidden mt-4 space-y-4 p-4 border rounded-lg bg-gray-50">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                        <input type="text" name="custom_subject" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Email subject...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                        <textarea name="custom_body" rows="8" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Write your message here..."></textarea>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="requires_acknowledgement" value="1" class="mr-2">
                            <span class="text-sm text-gray-700">Request confirmation of receipt from investor</span>
                        </label>
                    </div>
                </div>
            </div>

            @error('template')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

                <!-- Document Selection -->
                <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Attach Documents</h3>
                    <p class="text-sm text-gray-500 mb-4">Only approved documents are shown. Select documents to attach to this email.</p>

                    <div class="mb-3">
                        <input type="text" id="doc_search" placeholder="Search documents..." 
                               class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>

                    <div class="space-y-2 max-h-64 overflow-y-auto" id="document_list">
                        @foreach($documents as $doc)
                        <label class="flex items-center p-2 border rounded cursor-pointer hover:bg-gray-50 document-item">
                            <input type="checkbox" name="document_ids[]" value="{{ $doc->id }}" class="mr-3">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 doc-name">{{ $doc->document_name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $doc->folder->folder_name ?? 'No folder' }} | 
                                    v{{ $doc->version }} | 
                                    {{ strtoupper($doc->file_type) }}
                                </p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Submit -->
                @if(!$investor)
                {{-- Bulk: hidden inputs for approval flow --}}
                <input type="hidden" name="is_bulk" value="1">
                <input type="hidden" name="bulk_recipient_type" value="{{ $recipients }}">
                <input type="hidden" name="bulk_recipient_stage" value="{{ $selectedStage ?? '' }}">
                @if($assignedToMe ?? false)
                    <input type="hidden" name="bulk_assigned_to_me" value="1">
                @endif
                <input type="hidden" id="bulk_recipient_ids_input" name="bulk_recipient_ids"
                       value="{{ json_encode($investors->pluck('id')->values()->all()) }}">
                @endif

                <div class="bg-white shadow sm:rounded-lg p-6">
                    @if(!$investor)
                    <div class="flex items-center gap-2 p-3 bg-amber-50 border border-amber-200 rounded-lg mb-4">
                        <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-amber-800">This bulk email will be <strong>submitted for admin approval</strong> before sending. Once approved, you will be able to send it to all {{ $investors->count() }} recipient(s).</p>
                    </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500">
                            Sent from: <strong>{{ auth()->user()->email }}</strong>
                        </p>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                            @if(!$investor)
                                Submit for Approval
                            @else
                                Send Email
                            @endif
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>
        const bulkUrl = '{{ route("investors.send-email.bulk") }}';

        function applyFilters() {
            const recipientType = document.getElementById('recipient_type').value;
            const stage         = document.getElementById('stage_filter')?.value || '';
            const assignedToMe  = document.getElementById('assigned_to_me')?.checked;

            const params = new URLSearchParams();
            params.set('recipient_type', recipientType);
            if (recipientType === 'stage' && stage) params.set('stage', stage);
            if (assignedToMe) params.set('assigned_to_me', '1');

            window.location.href = bulkUrl + '?' + params.toString();
        }

        function toggleStage(value) {
            const stageDiv = document.getElementById('stage_select');
            if (!stageDiv) return;
            if (value === 'stage') {
                stageDiv.classList.remove('invisible');
            } else {
                stageDiv.classList.add('invisible');
                document.getElementById('stage_filter').value = '';
                applyFilters();
            }
        }

        // Document search
        document.getElementById('doc_search')?.addEventListener('input', function() {
            const search = this.value.toLowerCase();
            document.querySelectorAll('.document-item').forEach(item => {
                const name = item.querySelector('.doc-name').textContent.toLowerCase();
                item.style.display = name.includes(search) ? '' : 'none';
            });
        });

            document.getElementById('preview_btn')?.addEventListener('click', function(e) {
                e.preventDefault();
                const template = document.querySelector('input[name="template"]:checked')?.value;
                if (!template) {
                    alert('Please select a template first.');
                    return;
                }

                if (template === 'custom') {
                    const subject = document.querySelector('input[name="custom_subject"]')?.value || '';
                    const body = document.querySelector('textarea[name="custom_body"]')?.value || '';

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("investors.send-email.preview") }}';
                    form.target = '_blank';

                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    form.appendChild(csrf);

                    [['template', template], ['custom_subject', subject], ['custom_body', body]].forEach(([name, value]) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = name;
                        input.value = value;
                        form.appendChild(input);
                    });

                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                } else {
                    window.open('{{ route("investors.send-email.preview") }}?template=' + template, '_blank');
                }
            });

        // Show/hide custom fields
            document.querySelectorAll('input[name="template"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const customFields = document.getElementById('custom_fields');
                    customFields.classList.toggle('hidden', this.value !== 'custom');
                });
            });
    </script>
</x-app-layout>