<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Draft — {{ $emailDraft->investor->organization_name ?? $emailDraft->investor->legal_entity_name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('email-drafts.preview', $emailDraft) }}" target="_blank"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded border border-gray-300">
                    Preview
                </a>
                <a href="{{ route('email-drafts.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    ← Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Status Banner --}}
            <div class="mb-4 p-3 rounded-lg
                @if($emailDraft->status === 'pending_approval') bg-yellow-50 border border-yellow-200
                @elseif($emailDraft->status === 'approved') bg-green-50 border border-green-200
                @else bg-gray-50 border border-gray-200
                @endif">
                <p class="text-sm font-medium
                    @if($emailDraft->status === 'pending_approval') text-yellow-800
                    @elseif($emailDraft->status === 'approved') text-green-800
                    @else text-gray-700
                    @endif">
                    Status: {{ ucfirst(str_replace('_', ' ', $emailDraft->status)) }}
                    @if($emailDraft->status === 'approved' && $emailDraft->approvedBy)
                        — Approved by {{ $emailDraft->approvedBy->name }} on {{ $emailDraft->approved_at->format('M d, Y H:i') }}
                    @endif
                </p>
            </div>

            {{-- Approve action — standalone form, NOT nested inside the edit form --}}
            @can('approve', $emailDraft)
                @if($emailDraft->status === 'pending_approval')
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
                    <p class="text-sm text-green-800 font-medium">This draft is awaiting your approval.</p>
                    <form method="POST" action="{{ route('email-drafts.approve', $emailDraft) }}">
                        @csrf
                        <button type="submit"
                                class="bg-green-600 hover:bg-green-800 text-white font-bold py-2 px-5 rounded"
                                onclick="return confirm('Approve this draft for sending?')">
                            ✓ Approve Draft
                        </button>
                    </form>
                </div>
                @endif
            @endcan

            {{-- Send action (creator only, when approved) — also standalone --}}
            @if($emailDraft->status === 'approved' && $emailDraft->created_by_user_id === auth()->id())
            <div class="mb-4 p-4 bg-teal-50 border border-teal-200 rounded-lg flex items-center justify-between">
                <p class="text-sm text-teal-800 font-medium">This draft is approved and ready to send.</p>
                <form method="POST" action="{{ route('email-drafts.send', $emailDraft) }}">
                    @csrf
                    <button type="submit"
                            class="bg-teal-600 hover:bg-teal-800 text-white font-bold py-2 px-5 rounded"
                            onclick="return confirm('Send this email now?')">
                        Send Email
                    </button>
                </form>
            </div>
            @endif

            {{-- Edit form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <form action="{{ route('email-drafts.update', $emailDraft) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">

                            {{-- On Behalf Of --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">On Behalf Of</label>
                                <select name="on_behalf_of_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">— Select person —</option>
                                    @foreach($onBehalfOptions as $person)
                                        <option value="{{ $person->id }}"
                                            {{ old('on_behalf_of_id', $emailDraft->on_behalf_of_id) == $person->id ? 'selected' : '' }}>
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
                                       value="{{ old('subject', $emailDraft->subject) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Body Template Selector --}}
                            @if($bodyTemplates->count() > 0)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Load Body Template</label>
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
                                <p class="mt-1 text-xs text-gray-500">This will replace the current body content.</p>
                            </div>
                            @endif

                            {{-- Body --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Body <span class="text-red-500">*</span>
                                </label>
                                <textarea name="body" id="email_body" rows="12" required
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('body', $emailDraft->body) }}</textarea>
                            </div>

                            {{-- Signature --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Signature</label>
                                <select name="signature_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">— No signature —</option>
                                    @foreach($signatures as $signature)
                                        <option value="{{ $signature->id }}"
                                            {{ old('signature_id', $emailDraft->signature_id) == $signature->id ? 'selected' : '' }}>
                                            {{ $signature->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- CC Placement Agent --}}
                            @php $investor = $emailDraft->investor; @endphp
                            @if($investor->source_of_introduction === 'placement_agent' && $investor->placement_agent_email)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">CC</label>
                                <label class="flex items-center space-x-2 p-3 bg-gray-50 border border-gray-200 rounded-md">
                                    <input type="checkbox" name="cc_placement_agent" value="1"
                                           {{ in_array($investor->placement_agent_email, old('cc_emails', $emailDraft->cc_emails ?? [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600">
                                    <span class="text-sm text-gray-700">
                                        CC Placement Agent:
                                        <strong>{{ $investor->placement_agent_name }}</strong>
                                        ({{ $investor->placement_agent_email }})
                                    </span>
                                </label>
                            </div>
                            @endif

                            {{-- Documents --}}
                            @if($documents->count() > 0)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Attach Documents</label>
                                <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3">
                                    @foreach($documents as $doc)
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="document_ids[]" value="{{ $doc->id }}"
                                                   {{ in_array($doc->id, old('document_ids', $emailDraft->document_ids ?? [])) ? 'checked' : '' }}
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

                        {{-- Form Action Buttons --}}
                        <div class="mt-8 flex items-center justify-end space-x-3">
                            <a href="{{ route('email-drafts.index') }}"
                               class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded">
                                Cancel
                            </a>
                            <button type="submit" name="submit_for_approval" value="0"
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded">
                                Save Changes
                            </button>
                            {{-- Submit for Approval: only for non-admins, only when not yet approved --}}
                            @cannot('approve', $emailDraft)
                                @if($emailDraft->status !== 'approved')
                                <button type="submit" name="submit_for_approval" value="1"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded">
                                    Submit for Approval
                                </button>
                                @endif
                            @endcannot
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
            if (body && confirm('This will replace the current body. Continue?')) {
                tinymce.get('email_body')?.setContent(body);
                if (subject) {
                    const subjectField = document.querySelector('input[name="subject"]');
                    if (subjectField) subjectField.value = subject;
                }
            } else {
                this.value = '';
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
        });
    </script>

</x-app-layout>
