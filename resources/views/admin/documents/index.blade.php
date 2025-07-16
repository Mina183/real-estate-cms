<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">All Uploaded Partner Documents</h2>
        <div class="mb-4">
            <a href="{{ route('dashboard') }}" class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="p-6">
        @if(session('success'))
            <div class="bg-green-200 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <table class="w-full table-auto border border-gray-300 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">Title</th>
                    <th class="border px-4 py-2">Original File</th>
                    <th class="border px-4 py-2">Partner</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2">Partner Response</th>
                    <th class="border px-4 py-2">Responses</th>
                    <th class="border px-4 py-2">Uploaded At</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                    <tr>
                        <td class="border px-4 py-2">{{ $doc->title }}</td>
                        <td class="border px-4 py-2">
                        <a href="{{ Storage::disk('private')->temporaryUrl($doc->file_path, now()->addMinutes(10)) }}" target="_blank" class="text-blue-600 hover:underline">
                            {{ $doc->filename }}
                        </a>
                        </td>
                        <td class="border px-4 py-2">
                            {{ $doc->partner ? $doc->partner->name : 'All Partners' }}
                        </td>
                    <td class="border px-4 py-2">
                        @if ($doc->partner_id === null)
                            @php
                                $totalPartners = \App\Models\User::where('role', 'channel_partner')->count();
                                $responses = $doc->responses ?? collect();
                                $acknowledged = $responses->where('status', 'acknowledged')->count();
                                $completed = $responses->where('status', 'complete')->count();
                                $hasUploads = $responses->filter(fn($r) => $r->response_file_path)->count() > 0;
                                $isReviewOnly = !$hasUploads && $responses->count() > 0;
                            @endphp

                            @if ($isReviewOnly)
                                @if ($acknowledged === 0)
                                    <span class="text-orange-600 font-medium">Waiting for Partner Reviews</span>
                                @elseif ($acknowledged < $totalPartners)
                                    <span class="text-blue-600 font-medium">
                                        Some Responses Pending Review ({{ $acknowledged }} of {{ $totalPartners }} reviewed)
                                    </span>
                                @else
                                    <span class="text-green-600 font-medium">
                                        All Docs Reviewed ({{ $acknowledged }} of {{ $totalPartners }})
                                    </span>
                                @endif
                            @else
                                @if ($responses->count() === 0)
                                    <span class="text-orange-600 font-medium">Waiting for Partner Responses</span>
                                @elseif ($completed < $responses->count())
                                    <span class="text-blue-600 font-medium">
                                        Some Responses Pending Review ({{ $completed }} of {{ $responses->count() }} approved)
                                    </span>
                                @elseif ($completed === $totalPartners)
                                    <span class="text-green-600 font-medium">
                                        All Responses Reviewed ({{ $completed }} of {{ $totalPartners }})
                                    </span>
                                @else
                                    <span class="text-yellow-600 font-medium">
                                        Waiting on Remaining Partners ({{ $completed }} of {{ $totalPartners }} approved)
                                    </span>
                                @endif
                            @endif
                        @else
                            @php
                                $statusLabels = [
                                    'waiting_partner_action' => ['label' => 'Partner Action Required', 'class' => 'text-orange-600'],
                                    'waiting_admin_approval' => ['label' => 'Awaiting Admin Approval', 'class' => 'text-blue-600'],
                                    'review_only' => ['label' => 'For Review', 'class' => 'text-yellow-600'],
                                    'acknowledged' => ['label' => 'Acknowledged', 'class' => 'text-green-600'],
                                    'complete' => ['label' => 'Completed', 'class' => 'text-green-600'],
                                ];
                                $status = $statusLabels[$doc->status] ?? ['label' => ucfirst($doc->status), 'class' => 'text-gray-500'];
                            @endphp
                            <span class="{{ $status['class'] }} font-medium">{{ $status['label'] }}</span>
                        @endif
                    </td>
                        <td class="border px-4 py-2">
                            @if($doc->partner_id === null)
                                <span class="text-gray-400 italic">See Responses Column</span>
                            @elseif($doc->response_file_path)
                            <a href="{{ Storage::disk('private')->temporaryUrl($doc->response_file_path, now()->addMinutes(10)) }}" target="_blank" class="text-blue-600 hover:underline">
                                View Response
                            </a>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $doc->response_uploaded_at ? $doc->response_uploaded_at->format('Y-m-d H:i') : '' }}
                                </div>
                            @else
                                <span class="text-gray-400 italic">No response</span>
                            @endif
                        </td>
                        <td class="border px-4 py-2">
                            @if($doc->partner_id === null)
                                <a href="{{ route('admin.documents.show', $doc->id) }}" class="text-blue-600 hover:underline">
                                    {{ $doc->responses->count() }} response(s)
                                </a>
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="border px-4 py-2">{{ $doc->created_at->format('Y-m-d H:i') }}</td>
                        <td class="border px-4 py-2">
                            <div class="flex flex-col gap-1 items-start">
                                {{-- Keep your existing approval logic untouched --}}
                                @if($doc->status === 'waiting_admin_approval')
                                    <form action="{{ route('admin.documents.approve', $doc->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="bg-green-700 text-white px-3 py-1 rounded text-sm">Mark as Complete</button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400 italic">No action</span>
                                @endif

                                {{-- Add delete button (separate action) --}}
                                <form action="{{ route('admin.documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Delete this document?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded text-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-gray-500">No documents found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $documents->links() }}
        </div>
    </div>
</x-app-layout>