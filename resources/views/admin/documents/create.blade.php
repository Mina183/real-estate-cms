<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-[#0e2442]">Manage and Upload Documents for Partners</h2>
        <div class="mb-4">
            <a href="{{ route('dashboard') }}" class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="p-6 space-y-6">

        {{-- ✅ Success message --}}
        @if(session('success'))
            <div class="bg-green-200 text-green-800 p-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- ❌ Error message --}}
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 p-3 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 📄 View All Documents + red dot --}}
        <div class="bg-[#0e2442] border border-[#afac9b] rounded p-4 shadow-sm">
            <h3 class="text-base font-semibold text-white mb-2">Manage Documents</h3>
            <p class="text-xs text-gray-500">
            {{--  Debug: showRedDot = {{ $showRedDot ? 'true' : 'false' }} --}}
            </p>
            <a href="{{ route('admin.documents.index') }}"
                class="relative inline-block bg-gray-200 text-[#0e2442] text-sm px-4 py-2 rounded hover:bg-gray-300 transition">
                View All Documents
                @if($showRedDot)
                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-red-600 rounded-full animate-pulse border border-white"></span>
                @endif
            </a>
        </div>

        {{-- 📤 Upload Form --}}
        <div class="bg-white border border-[#afac9b] rounded p-6 shadow-md">
            <h3 class="text-base font-semibold text-[#0e2442] mb-4">Upload New Document</h3>

            <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label class="block font-medium text-sm text-[#0e2442]">Document Title</label>
                    <input type="text" name="title" class="form-input w-full border-gray-300 rounded" value="{{ old('title') }}" required>
                </div>

                <div>
                    <label class="block font-medium text-sm text-[#0e2442]">File</label>
                    <input type="file" name="file" required
                        class="block w-full text-sm text-gray-700 border border-gray-300 rounded-md cursor-pointer bg-white 
                               file:bg-[#0e2442] file:text-white file:border-0 file:px-4 file:py-2 file:rounded file:cursor-pointer 
                               hover:file:bg-[#1b3a66] transition">
                </div>

                <div>
                    <label class="block font-medium text-sm text-[#0e2442]">Assign to Partner (optional)</label>
                    <select name="partner_id" class="form-select w-full border-gray-300 rounded">
                        <option value="">All Partners</option>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}" {{ old('partner_id') == $partner->id ? 'selected' : '' }}>
                                {{ $partner->name }} ({{ $partner->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="inline-flex items-center text-sm text-[#0e2442]">
                        <input type="checkbox" name="requires_response" value="1" class="mr-2">
                        Partner must upload a response
                    </label>
                </div>

                <div>
                    <button type="submit" class="bg-[#0e2442] text-white px-6 py-2 rounded hover:bg-[#1b3a66] transition">
                        Upload Document
                    </button>
                </div>
            </form>
        </div>
{{--
        {{-- 🔍 Debug: Shared documents that triggered red dot --}}
        @if($triggeringSharedDocs->isNotEmpty())
            <div class="bg-yellow-100 text-sm text-yellow-800 p-2 rounded">
                <strong>Debug:</strong> Shared documents triggering red dot:
                <ul class="list-disc ml-6 mt-2">
                    @foreach($triggeringSharedDocs as $doc)
                        <li>ID: {{ $doc->id }} | Title: {{ $doc->title }} | Status: {{ $doc->status ?? 'NULL' }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 🔍 Debug: Partner responses that triggered red dot --}}
        @if($triggeringDocs->isNotEmpty())
            <div class="bg-red-100 text-sm text-red-800 p-2 rounded">
                <strong>Debug:</strong> Partner responses triggering red dot:
                <ul class="list-disc ml-6 mt-2">
                    @foreach($triggeringDocs as $response)
                        <li>
                            Response ID: {{ $response->id }}
                            | Document ID: {{ $response->document_id }}
                            | Status: {{ $response->status ?? 'NULL' }}
                            | Partner: {{ $response->partner->name ?? 'Unknown' }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
--}}
    </div>
</x-app-layout>