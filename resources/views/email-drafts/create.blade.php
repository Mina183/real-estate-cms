<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Compose Email — {{ $investor->organization_name ?? $investor->legal_entity_name }}
            </h2>
            <a href="{{ route('investors.show', $investor) }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Cancel
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('email-drafts.store') }}" method="POST" onsubmit="tinymce.triggerSave(); return true;">
                        @csrf
                        <input type="hidden" name="investor_id" value="{{ $investor->id }}">

                        <div class="space-y-6">

                            {{-- On Behalf Of --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    On Behalf Of
                                </label>
                                <select name="on_behalf_of_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">— Select person —</option>
                                    @foreach($onBehalfOptions as $person)
                                        <option value="{{ $person->id }}" {{ old('on_behalf_of_id') == $person->id ? 'selected' : '' }}>
                                            {{ $person->name }}{{ $person->title ? ' — ' . $person->title : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Subject --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Subject <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="subject" required
                                       value="{{ old('subject') }}"
                                       placeholder="Email subject line"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Body Template Selector --}}
                            @if($bodyTemplates->count() > 0)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Load Body Template
                                </label>
                                <select id="body_template_selector"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">— Select template to load —</option>
                                    @foreach($bodyTemplates as $template)
                                        <option value="{{ $template->body }}"
                                                data-subject="{{ $template->subject_suggestion }}">
                                            {{ $template->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Selecting a template will populate the body below. You can then edit it.</p>
                            </div>
                            @endif

                            {{-- Body --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Body <span class="text-red-500">*</span>
                                </label>
                                <textarea name="body" id="email_body" rows="12"
                                          placeholder="Write your email body here..."
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('body') }}</textarea>
                            </div>

                            {{-- Signature --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Signature
                                </label>
                                <select name="signature_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">— No signature —</option>
                                    @foreach($signatures as $signature)
                                        <option value="{{ $signature->id }}" {{ old('signature_id') == $signature->id ? 'selected' : '' }}>
                                            {{ $signature->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- CC --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">CC</label>
                                <div class="space-y-2">
                                    @if($investor->source_of_introduction === 'placement_agent' && $investor->placement_agent_email)
                                    <label class="flex items-center space-x-2 p-3 bg-gray-50 border border-gray-200 rounded-md">
                                        <input type="checkbox" name="cc_placement_agent" value="1"
                                               {{ old('cc_placement_agent') ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-blue-600">
                                        <span class="text-sm text-gray-700">
                                            CC Placement Agent:
                                            <strong>{{ $investor->placement_agent_name }}</strong>
                                            ({{ $investor->placement_agent_email }})
                                        </span>
                                    </label>
                                    @endif
                                    <input type="email" name="cc_custom_email"
                                           value="{{ old('cc_custom_email') }}"
                                           placeholder="Custom CC email address (optional)"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                </div>
                            </div>

                            {{-- Documents --}}
                            @if($documents->count() > 0)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Attach Documents
                                </label>
                                <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3">
                                    @foreach($documents as $doc)
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="document_ids[]" value="{{ $doc->id }}"
                                                   {{ in_array($doc->id, old('document_ids', [])) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-blue-600">
                                            <span class="text-sm text-gray-700">
                                                {{ $doc->document_name }}
                                                <span class="text-xs text-gray-400">v{{ $doc->version }} — {{ $doc->folder->folder_name ?? '—' }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-8 flex items-center justify-end space-x-3">
                            <a href="{{ route('investors.show', $investor) }}"
                               class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded">
                                Cancel
                            </a>
                            <button type="submit" name="submit_for_approval" value="0"
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded">
                                Save as Draft
                            </button>
                            <button type="submit" name="submit_for_approval" value="1"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded">
                                Submit for Approval
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('body_template_selector')?.addEventListener('change', function() {
            const body = this.value;
            const subject = this.options[this.selectedIndex]?.getAttribute('data-subject');
            if (body) {
                tinymce.get('email_body')?.setContent(body);
            }
            if (subject) {
                const subjectField = document.querySelector('input[name="subject"]');
                if (subjectField && !subjectField.value) {
                    subjectField.value = subject;
                }
            }
        });
    </script>

    <script src="https://cdn.tiny.cloud/1/cpo4gfv8nwq74g9b2ert0jfc2n8tv3z60s2uiqcx4wqovftg/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        <script>
        tinymce.init({
            selector: 'textarea[name="body"]',
            plugins: 'lists link',
            toolbar: 'bold italic underline | bullist numlist | link | removeformat',
            menubar: false,
            height: 400,
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
            branding: false,
            setup: function(editor) {
                editor.on('submit', function() {
                    editor.save();
                });
            }
        });
        </script>

</x-app-layout>