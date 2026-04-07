<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Template: {{ $emailBodyTemplate->name }}</h2>
            <a href="{{ route('email-body-templates.index') }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back
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

                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-800">
                            Use <code class="bg-blue-100 px-1 rounded">@{{ investor_name }}</code> in the body to insert the investor's organisation name automatically.
                        </p>
                    </div>

                    <form action="{{ route('email-body-templates.update', $emailBodyTemplate) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Template Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" required
                                       value="{{ old('name', $emailBodyTemplate->name) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Subject Suggestion
                                </label>
                                <input type="text" name="subject_suggestion"
                                       value="{{ old('subject_suggestion', $emailBodyTemplate->subject_suggestion) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Body <span class="text-red-500">*</span>
                                </label>
                                <textarea name="body" id="body-editor" rows="16"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm">{{ old('body', $emailBodyTemplate->body) }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Do not include disclaimer or signature — these are added automatically.</p>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" value="1"
                                       {{ old('is_active', $emailBodyTemplate->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 mr-2">
                                <label class="text-sm font-medium text-gray-700">Active (visible in compose form)</label>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-end space-x-3">
                            <a href="{{ route('email-body-templates.index') }}"
                               class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded">
                                Update Template
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.tiny.cloud/1/cpo4gfv8nwq74g9b2ert0jfc2n8tv3z60s2uiqcx4wqovftg/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#body-editor',
            plugins: 'lists link',
            toolbar: 'bold italic underline | bullist numlist | link | removeformat',
            menubar: false,
            height: 400,
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
            branding: false,
            relative_urls: false,
            remove_script_host: false,
            convert_urls: false,
        });

        document.querySelector('form').addEventListener('submit', function () {
            tinymce.triggerSave();
        });
    </script>

</x-app-layout>