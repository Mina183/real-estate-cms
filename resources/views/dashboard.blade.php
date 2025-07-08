<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard
        </h2>
        <div class="text-sm text-gray-500 dark:text-gray-400">
            Logged in as: {{ auth()->user()->role }}
        </div>
    </x-slot>

    <div class="min-h-screen bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex min-h-screen">
            @if(auth()->user()->role === 'channel_partner')
                <!-- Partner Sidebar -->
                <aside class="w-72 bg-[#0e2442] text-gray-100 py-8 px-4 space-y-4 rounded-r-lg shadow-md">
                    <h3 class="text-lg font-bold mb-4">Navigation</h3>
                    <nav class="flex flex-col gap-2">
                        <a href="{{ route('lead-sources.index') }}"
                           class="block bg-white text-[#0e2442] px-4 py-2 rounded hover:bg-gray-200 font-semibold transition">
                            Register / Review Lead Sources
                        </a>
                        <a href="{{ route('clients.index') }}"
                           class="block bg-white text-[#0e2442] px-4 py-2 rounded hover:bg-gray-200 font-semibold transition">
                            Register / Review / Edit Clients
                        </a>
                        <a href="{{ route('partner.documents.index') }}"
                           class="relative block bg-white text-[#0e2442] px-4 py-2 rounded hover:bg-gray-200 font-semibold transition">
                            Documents
                            @if(!empty($showRedDot))
                                <span class="absolute top-1 right-2 inline-block w-3 h-3 bg-red-600 rounded-full animate-pulse"></span>
                            @endif
                        </a>
                    </nav>
                </aside>

                <main class="flex-1 py-6 px-8 bg-gray-100">
                    <div class="max-w-5xl mx-auto">
                        <h3 class="text-2xl font-bold text-[#0e2442] mb-4">Welcome, {{ auth()->user()->name }}!</h3>
                        <p class="text-gray-700">
                            Use the sidebar to access and manage your lead sources and client data.
                        </p>
                    </div>
                </main>

            @elseif(auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')
                <!-- Admin Sidebar -->
                <aside class="w-72 bg-[#0e2442] text-gray-100 py-8 px-4 space-y-4 rounded-r-lg shadow-md">
                    <h3 class="text-lg font-bold mb-4">Navigation</h3>
                    <nav class="flex flex-col gap-2">
                        <a href="{{ route('admin.clients.index') }}"
                           class="block bg-white text-[#0e2442] px-4 py-2 rounded hover:bg-gray-200 font-semibold transition">
                            Review Partners and Clients
                        </a>
                        <a href="{{ route('admin.documents.create') }}"
                           class="relative block bg-white text-[#0e2442] px-4 py-2 rounded hover:bg-gray-200 font-semibold transition">
                            Manage Partner Documents and Forms
                            @if(!empty($showRedDot))
                                <span class="absolute top-1 right-2 inline-block w-3 h-3 bg-red-600 rounded-full animate-pulse"></span>
                            @endif
                        </a>
                    </nav>
                </aside>
            @endif
        </div>
    </div>

    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')
        <div class="max-w-6xl mx-auto mt-10 bg-white p-4 border rounded shadow">
            <h3 class="text-lg font-bold text-red-600 mb-2">Debug: Admin-triggered Red Dot Documents</h3>
            @if($adminTriggeringDocs->isEmpty())
                <p class="text-green-600">✅ No documents triggered the red dot.</p>
            @else
                <ul class="list-disc list-inside text-sm text-gray-800">
                    @foreach($adminTriggeringDocs as $doc)
                        <li>
                            <strong>{{ $doc->title }}</strong> (Doc ID: {{ $doc->id }})
                            <ul class="ml-4 list-disc text-gray-700">
                                @php
                                    $responses = \App\Models\PartnerDocumentResponse::where('document_id', $doc->id)
                                        ->whereIn('status', ['waiting_partner_action', 'review_only', null])
                                        ->with('partner') // assumes belongsTo(User::class, 'partner_id')
                                        ->get();
                                @endphp
                                @forelse($responses as $response)
                                    <li>
                                        Partner: {{ $response->partner->name ?? 'Unknown' }},
                                        Status: <span class="font-semibold">{{ $response->status ?? 'null' }}</span>
                                    </li>
                                @empty
                                    <li>No responses found (or all completed).</li>
                                @endforelse
                            </ul>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    @if(auth()->user()->role === 'channel_partner')
        <div class="max-w-6xl mx-auto mt-10 bg-white p-4 border rounded shadow">
            <h3 class="text-lg font-bold text-red-600 mb-2">Debug: Partner-triggered Red Dot Documents</h3>
            @if(empty($partnerTriggeringDocs))
                <p class="text-green-600">✅ No documents triggered the red dot.</p>
            @else
                <ul class="list-disc list-inside text-sm text-gray-800">
                    @foreach($partnerTriggeringDocs as $doc)
                        <li><strong>{{ $doc->title }}</strong> (status: {{ $doc->status ?? 'null' }})</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
</x-app-layout>