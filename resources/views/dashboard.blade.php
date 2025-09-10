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
            {{-- ========================= CHANNEL PARTNER VIEW ========================= --}}
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
                            @if(!empty($documentRedDot))
                                <span class="absolute top-1 right-2 inline-block w-3 h-3 bg-red-600 rounded-full animate-pulse"></span>
                            @endif
                        </a>
                        <a href="{{ route('calendar.index') }}"
                           class="relative block bg-white text-[#0e2442] px-4 py-2 rounded hover:bg-gray-200 font-semibold transition">
                            📅 Team Calendar
                            @if(!empty($calendarRedDot))
                                <span class="absolute top-1 right-2 inline-block w-3 h-3 bg-red-600 rounded-full animate-pulse"></span>
                            @endif
                        </a>
                    </nav>
                </aside>

                <main class="flex-1 py-6 px-8 bg-gray-100">
                    <div class="max-w-5xl mx-auto">
                        <h3 class="text-2xl font-bold text-[#0e2442] mb-4">Welcome, {{ auth()->user()->name }}!</h3>
                        <p class="text-gray-700">Use the sidebar to access and manage your lead sources and client data.</p><br>

                        {{-- ✅ Pending Meeting Invitations --}}
                        @if($pendingMeetingInvitations->count())
                            <div class="mb-6 bg-yellow-100 border-l-4 border-yellow-400 p-4 rounded">
                                <h4 class="text-lg font-semibold text-yellow-800 mb-2">🕒 Meetings Pending Your Response</h4>
                                <ul class="list-disc list-inside text-gray-800">
                                    @foreach($pendingMeetingInvitations as $meeting)
                                        <li>{{ $meeting->title }} – {{ \Carbon\Carbon::parse($meeting->start_time)->format('M d, H:i') }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- ✅ Upcoming Accepted Meetings --}}
                        @if($upcomingAcceptedMeetings->count())
                            <div class="mb-6 bg-green-100 border-l-4 border-green-400 p-4 rounded">
                                <h4 class="text-lg font-semibold text-green-800 mb-2">✅ Upcoming Accepted Meetings</h4>
                                <ul class="list-disc list-inside text-gray-800">
                                    @foreach($upcomingAcceptedMeetings as $meeting)
                                        <li>{{ $meeting->title }} – {{ \Carbon\Carbon::parse($meeting->start_time)->format('M d, H:i') }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    @if($partnerTriggeringDocs->isNotEmpty())
                    <div class="mt-6">
                        <h4 class="text-lg font-semibold text-blue-800 mb-2">📄 Documents Needing Your Attention</h4>
                        <ul class="list-disc list-inside text-gray-800 space-y-2">
                            @foreach($partnerTriggeringDocs as $doc)
                                <li>
                                    <strong>{{ $doc->title }}</strong>
                                    <span class="text-sm text-gray-500">
                                        — {{ $doc->partner_id ? 'Direct' : 'Shared' }} Document
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                </main>

            {{-- ========================= ADMIN VIEW ========================= --}}
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
                            @if(!empty($documentRedDot))
                                <span class="absolute top-1 right-2 inline-block w-3 h-3 bg-red-600 rounded-full animate-pulse"></span>
                            @endif
                        </a>
                        <a href="{{ route('calendar.index') }}"
                           class="relative block bg-white text-[#0e2442] px-4 py-2 rounded hover:bg-gray-200 font-semibold transition">
                            📅 Team Calendar
                            @if(!empty($calendarRedDot))
                                <span class="absolute top-1 right-2 inline-block w-3 h-3 bg-red-600 rounded-full animate-pulse"></span>
                            @endif
                        </a>
                        <a href="{{ route('admin.meeting.proposals') }}"
                            class="relative block bg-white text-[#0e2442] px-4 py-2 rounded hover:bg-gray-200 font-semibold transition">
                            📋 Meeting Proposals
                            @if(!empty($proposalsRedDot))
                                <span class="absolute top-1 right-2 inline-block w-3 h-3 bg-red-600 rounded-full animate-pulse"></span>
                            @endif
                        </a>
                    </nav>
                </aside>

                <main class="flex-1 py-6 px-8 bg-gray-100">
                    <div class="max-w-5xl mx-auto">
                        <h3 class="text-2xl font-bold text-[#0e2442] mb-4">Admin Dashboard</h3>

                        {{-- ✅ Admin Meeting Overview --}}
                        @if($adminUpcomingMeetings->count())
                            <div class="mb-6 bg-blue-100 border-l-4 border-blue-400 p-4 rounded">
                                <h4 class="text-lg font-semibold text-blue-800 mb-2">📅 Upcoming Meetings Overview</h4>
                                <ul class="list-disc list-inside text-gray-800 space-y-3">
                                    @foreach($adminUpcomingMeetings as $meeting)
                                        <li>
                                            <strong>{{ $meeting->title }}</strong> – {{ \Carbon\Carbon::parse($meeting->start_time)->format('M d, H:i') }}
                                            @php
                                               $unanswered = optional($meeting->attendees)->filter(fn($partner) => is_null($partner->pivot->is_accepted)) ?? collect();
                                            @endphp
                                            @if($unanswered->isEmpty())
                                                <div class="text-green-700 ml-6">✅ Accepted by all partners.</div>
                                            @else
                                                <div class="text-yellow-800 ml-6">
                                                    ⚠️ Pending responses from:
                                                    <ul class="list-disc list-inside ml-4">
                                                        @foreach($unanswered as $p)
                                                            <li>{{ $p->name }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Pending Meeting Proposals --}}
                        @if($pendingProposals->count())
                            <div class="mb-6 bg-purple-100 border-l-4 border-purple-400 p-4 rounded">
                                <h4 class="text-lg font-semibold text-purple-800 mb-2">📋 Pending Meeting Proposals ({{ $pendingProposals->count() }})</h4>
                                <ul class="list-disc list-inside text-gray-800 space-y-2">
                                    @foreach($pendingProposals as $proposal)
                                        <li>
                                            <strong>{{ $proposal->title }}</strong> 
                                            by {{ $proposal->creator->name }} – {{ $proposal->start_time->format('M d, H:i') }}
                                        </li>
                                    @endforeach
                                </ul>
                                <a href="{{ route('admin.meeting.proposals') }}" class="text-purple-600 hover:underline mt-2 inline-block">
                                    Review all proposals →
                                </a>
                            </div>
                        @endif
                    </div>
                    
                    @if($adminTriggeringDocs->isNotEmpty())
                    <div class="mt-6">
                        <h4 class="text-lg font-semibold text-blue-800 mb-2">📁 Documents Awaiting Partner Actions or Admin Review</h4>
                        <ul class="list-disc list-inside text-gray-800 space-y-2">
                            @foreach($adminTriggeringDocs as $doc)
                                <li>
                                    <strong>{{ $doc->title }}</strong>
                                    <span class="text-sm text-gray-500"> — Shared Document</span>
                                    <a href="{{ route('admin.documents.show', $doc->id) }}" class="text-blue-600 hover:underline ml-2">Review</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                </main>
            @endif
        </div>
    </div>
 @if(false)
    {{-- Debug Sections --}}
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
                                        ->with('partner')->get();
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
@endif
</x-app-layout>