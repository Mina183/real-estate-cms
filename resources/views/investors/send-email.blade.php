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

            <form method="POST" action="{{ route('investors.send-email') }}">
                @csrf

                @if($investor)
                    <input type="hidden" name="investor_id" value="{{ $investor->id }}">
                    <input type="hidden" name="recipient_type" value="single">
                @endif

                <!-- Recipient Selection (bulk only) -->
                @if(!$investor)
                <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recipients</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Send To</label>
                        <select name="recipient_type" id="recipient_type" class="block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="all">All Investors</option>
                            <option value="stage" {{ $recipients === 'stage' ? 'selected' : '' }}>Investors by Stage</option>
                        </select>
                    </div>

                    <div id="stage_select" class="{{ $recipients === 'stage' ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Stage</label>
                        <select name="stage" class="block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Select Stage --</option>
                            @foreach($stages as $stage)
                                <option value="{{ $stage }}" {{ ($selectedStage ?? '') === $stage ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', ucfirst($stage)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if(isset($investors) && $investors->count() > 0)
                        <div class="mt-4 p-3 bg-blue-50 rounded-md">
                            <p class="text-sm text-blue-700">{{ $investors->count() }} investor(s) will receive this email.</p>
                        </div>
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
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500">
                            Email will be sent from: <strong>{{ auth()->user()->email }}</strong>
                        </p>
                        <a href="#" id="preview_btn" target="_blank"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            👁 Preview
                        </a>
                        <button type="submit" 
                                class="bg-teal-600 hover:bg-teal-700 text-white font-bold py-2 px-6 rounded">
                            Send Email
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>
        // Stage select toggle
        document.getElementById('recipient_type')?.addEventListener('change', function() {
            const stageDiv = document.getElementById('stage_select');
            stageDiv.classList.toggle('hidden', this.value !== 'stage');
        });

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