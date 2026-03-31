<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Document Access Links —
                {{ $investor->organization_name ?? $investor->legal_entity_name ?? 'Investor #' . $investor->id }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('document-access-links.create', $investor) }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    + Generate Link
                </a>
                <a href="{{ route('investors.show', $investor) }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    ← Back to Investor
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($links->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No links generated yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Generate a link to allow this investor to request document access.</p>
                            <div class="mt-6">
                                <a href="{{ route('document-access-links.create', $investor) }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    + Generate Link
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($links as $link)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ $link->label ?: $link->package->name }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                Package: {{ $link->package->name }} &bull;
                                                Created by {{ $link->createdBy->name ?? '—' }} on {{ $link->created_at->format('d M Y') }}
                                            </p>
                                        </div>
                                        <form action="{{ route('document-access-links.destroy', $link) }}" method="POST"
                                              onsubmit="return confirm('Delete this link? All access requests for it will also be removed.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-medium">Delete</button>
                                        </form>
                                    </div>

                                    <div class="mt-3 flex items-center space-x-2">
                                        <input type="text" readonly
                                               value="{{ $link->public_url }}"
                                               onclick="this.select()"
                                               class="flex-1 text-xs border-gray-300 rounded bg-gray-50 px-2 py-1 focus:outline-none">
                                        <button onclick="navigator.clipboard.writeText('{{ $link->public_url }}')"
                                                class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded border border-gray-300">
                                            Copy
                                        </button>
                                    </div>

                                    {{-- Access Requests for this link --}}
                                    @if($link->accessRequests->isNotEmpty())
                                        <div class="mt-4">
                                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Access Requests</p>
                                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                                <thead>
                                                    <tr class="text-xs text-gray-500 text-left">
                                                        <th class="pb-1 pr-4 font-medium">Name</th>
                                                        <th class="pb-1 pr-4 font-medium">Email</th>
                                                        <th class="pb-1 pr-4 font-medium">Status</th>
                                                        <th class="pb-1 pr-4 font-medium">Expires</th>
                                                        <th class="pb-1 font-medium">Submitted</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-50">
                                                    @foreach($link->accessRequests as $req)
                                                        <tr>
                                                            <td class="py-1 pr-4 text-gray-900">{{ $req->requester_name }}</td>
                                                            <td class="py-1 pr-4 text-gray-600">{{ $req->requester_email }}</td>
                                                            <td class="py-1 pr-4">
                                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                    @if($req->status === 'pending') bg-yellow-100 text-yellow-800
                                                                    @elseif($req->status === 'approved') bg-green-100 text-green-800
                                                                    @else bg-red-100 text-red-800
                                                                    @endif">
                                                                    {{ ucfirst($req->status) }}
                                                                </span>
                                                            </td>
                                                            <td class="py-1 pr-4 text-gray-500 text-xs">
                                                                {{ $req->expires_at ? $req->expires_at->format('d M Y, H:i') : '—' }}
                                                            </td>
                                                            <td class="py-1 text-gray-500 text-xs">{{ $req->created_at->format('d M Y') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
