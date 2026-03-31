<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Package: {{ $documentPackage->name }}
            </h2>
            <div class="flex space-x-2">
                @can('update', $documentPackage)
                    <a href="{{ route('document-packages.edit', $documentPackage) }}"
                       class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Edit
                    </a>
                @endcan
                <a href="{{ route('document-packages.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    ← Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Package Details --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Package Details</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created By</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $documentPackage->createdBy->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $documentPackage->created_at->format('d M Y, H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $documentPackage->description ?: '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Documents in Package --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Documents ({{ $documentPackage->items->count() }})
                    </h3>
                    @if($documentPackage->items->isEmpty())
                        <p class="text-sm text-gray-500">No documents in this package.</p>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach($documentPackage->items as $item)
                                <li class="py-3 flex items-center justify-between">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">{{ $item->document->document_name ?? '—' }}</span>
                                        @if($item->document?->folder)
                                            <span class="ml-2 text-xs text-gray-400">{{ $item->document->folder->folder_name }}</span>
                                        @endif
                                    </div>
                                    @if($item->document?->file_type)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-700 uppercase">
                                            {{ $item->document->file_type }}
                                        </span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- Access Links --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Access Links ({{ $documentPackage->accessLinks->count() }})
                    </h3>
                    @if($documentPackage->accessLinks->isEmpty())
                        <p class="text-sm text-gray-500">No links generated yet. Generate links from an investor's profile.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Label</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Investor</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requests</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($documentPackage->accessLinks as $link)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ $link->label ?: '—' }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                @if($link->investor)
                                                    <a href="{{ route('investors.show', $link->investor) }}"
                                                       class="text-blue-600 hover:text-blue-900">
                                                        {{ $link->investor->organization_name ?? $link->investor->legal_entity_name ?? '#' . $link->investor->id }}
                                                    </a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-500">
                                                {{ $link->accessRequests->count() }}
                                                @php $pending = $link->accessRequests->where('status', 'pending')->count(); @endphp
                                                @if($pending > 0)
                                                    <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        {{ $pending }} pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <input type="text" readonly
                                                       value="{{ $link->public_url }}"
                                                       onclick="this.select()"
                                                       class="w-64 text-xs border-gray-300 rounded bg-gray-50 focus:outline-none">
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-500">{{ $link->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
