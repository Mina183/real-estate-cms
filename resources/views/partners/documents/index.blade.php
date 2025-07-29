<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Documents</h2>
        <div class="mb-4">
            <a href="{{ route('dashboard') }}" class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="p-6">
        @if(session('success'))
            <div class="bg-green-100 p-2 mb-4">{{ session('success') }}</div>
        @endif

        @if($documents->count())
        <div class="overflow-x-auto w-full">
            <table class="min-w-full table-auto border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border px-4 py-2">Title</th>
                        <th class="border px-4 py-2">Status</th>
                        <th class="border px-4 py-2">Uploaded At</th>
                        <th class="border px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $doc)
                        @php
                            $isShared = $doc->partner_id === null;
                            $response = $isShared ? $doc->responses->firstWhere('partner_id', Auth::id()) : null;

                            // Determine effective status
                            if ($isShared) {
                                $docStatus = $response->status ?? $doc->status;
                            } else {
                                $docStatus = $doc->status;
                            }

                            $statusLabels = [
                                'waiting_admin_approval' => ['label' => 'Awaiting Admin Approval', 'class' => 'text-blue-600'],
                                'review_only' => ['label' => 'Submitted (For Review)', 'class' => 'text-yellow-600'],
                                'acknowledged' => ['label' => 'Acknowledged', 'class' => 'text-green-600'],
                                'complete' => ['label' => 'Completed', 'class' => 'text-green-600'],
                                'waiting_partner_action' => ['label' => 'Partner Action Required', 'class' => 'text-orange-600'],
                            ];

                            $status = $statusLabels[$docStatus] ?? ['label' => ucfirst($docStatus), 'class' => 'text-gray-500'];
                        @endphp

                        <tr>
                            <td class="border px-4 py-2">{{ $doc->title }}</td>

                            <td class="border px-4 py-2">
                                <span class="{{ $status['class'] }} font-medium">{{ $status['label'] }}</span>
                                @if ($response && $response->response_file_path)
                                    <br>
                                    <a href="{{ Storage::disk('private')->temporaryUrl($response->response_file_path, now()->addMinutes(10)) }}" target="_blank" class="text-sm text-blue-600 underline">View Response</a>
                                @endif
                            </td>

                            <td class="border px-4 py-2">
                                {{ $doc->created_at->format('d M Y') }}
                                @if($doc->seen_by_partner_at)
                                    <span class="ml-2 text-xs text-green-500">• Seen</span>
                                @endif
                            </td>

                            <td class="border px-4 py-2">
                                <a href="{{ Storage::disk('private')->temporaryUrl($doc->file_path, now()->addMinutes(10)) }}" class="text-blue-600 underline" target="_blank">Download</a>

                                {{-- Upload Response --}}
                                @if($docStatus === 'waiting_partner_action')
                                    <form action="{{ route('partner.documents.uploadResponse', $doc->id) }}" method="POST" enctype="multipart/form-data" class="mt-2">
                                        @csrf
                                        <input type="file" name="response_file" required class="text-sm mb-1">
                                        <button type="submit" class="bg-[#0e2442] text-white px-3 py-1 rounded text-sm">Upload Response</button>
                                    </form>

                                {{-- Mark as Reviewed --}}
                                @elseif($docStatus === 'review_only' && !$response)
                                    <form method="POST" action="{{ route('partner.documents.acknowledge', $doc->id) }}" class="mt-2">
                                        @csrf
                                        <button type="submit" class="text-yellow-700 underline text-sm">Mark as Reviewed</button>
                                    </form>

                                {{-- Already reviewed --}}
                                @elseif($docStatus === 'acknowledged')
                                    <div class="text-gray-600 text-sm mt-2">You have reviewed this document.</div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
            {{ $documents->links() }}
        @else
            <p class="text-gray-600">No documents available.</p>
        @endif
    </div>
</x-app-layout>