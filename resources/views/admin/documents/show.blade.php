<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Document Details</h2>
        <div class="mb-4">
            <a href="{{ route('admin.documents.index') }}" class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                ← Back to Documents
            </a>
        </div>
    </x-slot>

    <div class="p-6 space-y-6">
        <div>
            <h3 class="text-lg font-semibold">{{ $doc->title }}</h3>
            <p class="text-gray-700">Uploaded for: {{ $doc->partner ? $doc->partner->name : 'All Partners' }}</p>
            <p class="text-sm text-gray-500">Uploaded on: {{ $doc->created_at->format('Y-m-d H:i') }}</p>
            <a href="{{ Storage::disk('private')->temporaryUrl($doc->file_path, now()->addMinutes(10)) }}" target="_blank" class="text-blue-600 underline">
                View Original File
            </a>
        </div>

        @if($doc->partner_id === null)
            <div>
                <h4 class="font-semibold text-md mb-2">Partner Responses</h4>
                <table class="w-full table-auto border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2">Partner</th>
                            <th class="border px-4 py-2">Response File</th>
                            <th class="border px-4 py-2">Uploaded At</th>
                            <th class="border px-4 py-2">Status / Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($doc->responses as $response)
                            <tr>
                                <td class="border px-4 py-2">{{ $response->partner->name ?? 'Unknown' }}</td>
                                <td class="border px-4 py-2">
                                <td class="border px-4 py-2">
                                    @if($response->response_file_path)
                                        <a href="{{ Storage::disk('private')->temporaryUrl($response->response_file_path, now()->addMinutes(10)) }}" target="_blank" class="text-blue-600 underline">
                                            View Response File
                                        </a>
                                    @else
                                        <span class="text-gray-500 italic">No file uploaded</span>
                                    @endif
                                </td>
                               <td class="border px-4 py-2">
                                    {{ $response->response_uploaded_at ? $response->response_uploaded_at->format('Y-m-d H:i') : 'N/A' }}
                                </td>
                                <td class="border px-4 py-2">
                                    @if($response->status === 'waiting_admin_approval')
                                        <form method="POST" action="{{ route('admin.responses.approve', $response->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs">
                                                Approve
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-green-600">
                                            {{ ucfirst(str_replace('_', ' ', $response->status)) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-gray-500 py-4">No responses yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>