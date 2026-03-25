<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Email Drafts</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            {{-- PENDING APPROVAL --}}
            @can('approve-drafts')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        ⏳ Pending Approval
                        @if($pendingDrafts->count() > 0)
                            <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                {{ $pendingDrafts->count() }}
                            </span>
                        @endif
                    </h3>

                    @if($pendingDrafts->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Investor</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created By</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pendingDrafts as $draft)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900">
                                            {{ $draft->investor->organization_name ?? $draft->investor->legal_entity_name }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">{{ $draft->subject }}</td>
                                        <td class="px-4 py-3 text-gray-500">{{ $draft->createdBy->name ?? '—' }}</td>
                                        <td class="px-4 py-3 text-gray-500">{{ $draft->created_at->format('M d, Y H:i') }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('email-drafts.edit', $draft) }}"
                                                   class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-1 px-3 rounded">
                                                    Review & Edit
                                                </a>
                                                <form method="POST" action="{{ route('email-drafts.approve', $draft) }}" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="bg-green-500 hover:bg-green-700 text-white text-xs font-bold py-1 px-3 rounded"
                                                            onclick="return confirm('Approve this draft for sending?')">
                                                        Approve
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No drafts pending approval.</p>
                    @endif
                </div>
            </div>
            @endcan

            {{-- MY DRAFTS --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">My Drafts</h3>

                    @if($myDrafts->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Investor</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($myDrafts as $draft)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900">
                                            {{ $draft->investor->organization_name ?? $draft->investor->legal_entity_name }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">{{ $draft->subject }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                                @if($draft->status === 'draft') bg-gray-100 text-gray-700
                                                @elseif($draft->status === 'pending_approval') bg-yellow-100 text-yellow-800
                                                @elseif($draft->status === 'approved') bg-green-100 text-green-800
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $draft->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-500">{{ $draft->created_at->format('M d, Y H:i') }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex space-x-2">
                                                @if($draft->status === 'draft')
                                                    <a href="{{ route('email-drafts.edit', $draft) }}"
                                                       class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-1 px-3 rounded">
                                                        Edit
                                                    </a>
                                                @endif
                                                @if($draft->status === 'approved')
                                                    <form method="POST" action="{{ route('email-drafts.send', $draft) }}" class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                                class="bg-teal-500 hover:bg-teal-700 text-white text-xs font-bold py-1 px-3 rounded"
                                                                onclick="return confirm('Send this email?')">
                                                            Send
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No drafts yet.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>