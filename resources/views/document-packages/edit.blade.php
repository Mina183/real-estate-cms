<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Package: {{ $documentPackage->name }}</h2>
            <a href="{{ route('document-packages.show', $documentPackage) }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <strong>Please fix the following errors:</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('document-packages.update', $documentPackage) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">

                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Package Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" required
                                       value="{{ old('name', $documentPackage->name) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" id="description" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $documentPackage->description) }}</textarea>
                            </div>

                            <div>
                                <label for="notify_user_id" class="block text-sm font-medium text-gray-700">
                                    Notify on Approval <span class="text-gray-400 font-normal">(optional)</span>
                                </label>
                                <select name="notify_user_id" id="notify_user_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">— No notification —</option>
                                    @foreach($notifiableUsers as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('notify_user_id', $documentPackage->notify_user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ str_replace('_', ' ', $user->role) }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">This person receives an email each time an access request for this package is approved.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Documents <span class="text-red-500">*</span>
                                </label>
                                @if($documents->isEmpty())
                                    <p class="text-sm text-gray-500">No approved documents found in the Data Room.</p>
                                @else
                                    <div class="border border-gray-300 rounded-md divide-y divide-gray-200 max-h-96 overflow-y-auto">
                                        @foreach($documents->groupBy(function($d) {
                                            $parent = $d->folder?->parent?->folder_name;
                                            $folder = $d->folder?->folder_name ?? 'Uncategorised';
                                            return $parent ? $parent . ' › ' . $folder : $folder;
                                        }) as $groupLabel => $docs)
                                            <div class="px-4 py-2 bg-gray-50 sticky top-0 z-10">
                                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $groupLabel }}</span>
                                            </div>
                                            @foreach($docs as $doc)
                                                <label class="flex items-center px-4 py-2 hover:bg-blue-50 cursor-pointer">
                                                    <input type="checkbox" name="document_ids[]" value="{{ $doc->id }}"
                                                           {{ in_array($doc->id, old('document_ids', $selectedIds)) ? 'checked' : '' }}
                                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 flex-shrink-0">
                                                    <span class="ml-3 text-sm text-gray-700 flex-1">{{ $doc->document_name }}</span>
                                                    <span class="ml-2 text-xs text-gray-400">v{{ $doc->version }}</span>
                                                    @if($doc->file_type)
                                                        <span class="ml-2 text-xs font-medium text-gray-400 uppercase bg-gray-100 px-1.5 py-0.5 rounded">{{ $doc->file_type }}</span>
                                                    @endif
                                                </label>
                                            @endforeach
                                        @endforeach
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Only general fund documents are shown. Investor-specific files are excluded.</p>
                                @endif
                            </div>

                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('document-packages.show', $documentPackage) }}"
                                   class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                                    Cancel
                                </a>
                                <button type="submit"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Save Changes
                                </button>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
