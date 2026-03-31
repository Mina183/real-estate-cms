<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Generate Access Link —
                {{ $investor->organization_name ?? $investor->legal_entity_name ?? 'Investor #' . $investor->id }}
            </h2>
            <a href="{{ route('document-access-links.index', $investor) }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
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

                    <form action="{{ route('document-access-links.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="investor_id" value="{{ $investor->id }}">

                        <div class="space-y-6">

                            <div>
                                <label for="document_package_id" class="block text-sm font-medium text-gray-700">
                                    Document Package <span class="text-red-500">*</span>
                                </label>
                                @if($packages->isEmpty())
                                    <p class="mt-1 text-sm text-red-600">
                                        No packages exist yet.
                                        <a href="{{ route('document-packages.create') }}" class="underline">Create one first.</a>
                                    </p>
                                @else
                                    <select name="document_package_id" id="document_package_id" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select a package…</option>
                                        @foreach($packages as $package)
                                            <option value="{{ $package->id }}"
                                                {{ old('document_package_id') == $package->id ? 'selected' : '' }}>
                                                {{ $package->name }}
                                                ({{ $package->items()->count() }} doc{{ $package->items()->count() === 1 ? '' : 's' }})
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div>
                                <label for="label" class="block text-sm font-medium text-gray-700">
                                    Label <span class="text-gray-400 font-normal">(optional)</span>
                                </label>
                                <input type="text" name="label" id="label"
                                       value="{{ old('label') }}"
                                       placeholder="e.g. Q1 2024 Pack — sent via email 31 Mar"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Internal note to identify this link.</p>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('document-access-links.index', $investor) }}"
                                   class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                                    Cancel
                                </a>
                                <button type="submit"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Generate Link
                                </button>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
