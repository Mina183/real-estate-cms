<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Document Access Requests
        </h2>
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
                    @if($requests->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requester</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Investor</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($requests as $req)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $req->requester_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $req->requester_email }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <a href="{{ route('document-packages.show', $req->link->package) }}"
                                                   class="text-blue-600 hover:text-blue-900">
                                                    {{ $req->link->package->name ?? '—' }}
                                                </a>
                                                @if($req->link->label)
                                                    <div class="text-xs text-gray-400">{{ $req->link->label }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                @if($req->link->investor)
                                                    <a href="{{ route('investors.show', $req->link->investor) }}"
                                                       class="text-blue-600 hover:text-blue-900">
                                                        {{ $req->link->investor->organization_name ?? $req->link->investor->legal_entity_name ?? '#' . $req->link->investor->id }}
                                                    </a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    @if($req->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($req->status === 'approved') bg-green-100 text-green-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    {{ ucfirst($req->status) }}
                                                </span>
                                                @if($req->status === 'approved' && $req->expires_at?->isPast())
                                                    <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">
                                                        Expired
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $req->expires_at ? $req->expires_at->format('d M Y, H:i') : '—' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $req->created_at->format('d M Y, H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                @if($req->status === 'pending')
                                                    <form action="{{ route('document-access-requests.approve', $req) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                                class="bg-green-500 hover:bg-green-700 text-white text-xs font-bold py-1 px-3 rounded">
                                                            Approve
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('document-access-requests.reject', $req) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                                class="bg-red-500 hover:bg-red-700 text-white text-xs font-bold py-1 px-3 rounded">
                                                            Reject
                                                        </button>
                                                    </form>
                                                @elseif($req->status === 'approved')
                                                    <span class="text-gray-400 text-xs">
                                                        Approved by {{ $req->approvedBy->name ?? '—' }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 text-xs">Rejected</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $requests->links() }}</div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-sm text-gray-500">No access requests yet.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
