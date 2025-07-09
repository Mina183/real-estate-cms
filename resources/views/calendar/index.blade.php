<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            📅 Team Calendar
        </h2>
        <div class="mb-4">
            <a href="{{ route('dashboard') }}" class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                ← Back to Dashboard
            </a>
        </div>
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')
            <div class="mb-6">
                <a href="{{ route('meetings.create') }}"
                   class="inline-block bg-[#0e2442] text-white font-semibold px-4 py-2 rounded hover:bg-opacity-90 transition">
                    ➕ Schedule New Meeting
                </a>
            </div>
        @endif
    </x-slot>

    <div class="py-10 px-6 max-w-6xl mx-auto">
    @if(session('success'))
        <div class="mb-4 px-4 py-2 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif


        {{-- ✅ Show only pending Meeting Invitations --}}
        @if(auth()->user()->role === 'channel_partner' && isset($meetings) && $meetings->contains(fn($m) => is_null($m->pivot->is_accepted)))
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">📝 Meeting Invitations</h3>

                @foreach($meetings as $meeting)
                    @php $pivot = $meeting->pivot; @endphp

                    @if (is_null($pivot->is_accepted))
                        <div class="border p-4 rounded mb-4 bg-gray-50">
                            <p class="font-medium text-gray-900">📌 {{ $meeting->title }}</p>
                            <p class="text-sm text-gray-700">📅 {{ \Carbon\Carbon::parse($meeting->scheduled_for)->format('l, jS F Y \a\t H:i') }}</p>

                            <div class="mt-3 flex space-x-2">
                                <form method="POST" action="{{ route('meetings.respond', $meeting->id) }}">
                                    @csrf
                                    <input type="hidden" name="response" value="1">
                                    <button type="submit" class="px-4 py-1 bg-green-600 text-white rounded hover:bg-green-700">Accept</button>
                                </form>

                                <form method="POST" action="{{ route('meetings.respond', $meeting->id) }}">
                                    @csrf
                                    <input type="hidden" name="response" value="0">
                                    <button type="submit" class="px-4 py-1 bg-red-600 text-white rounded hover:bg-red-700">Decline</button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- Calendar --}}
        <div class="py-6 px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <script>
        window.userRole = "{{ auth()->user()->role }}";
    </script>

    @vite(['resources/js/calendar.js'])
</x-app-layout>

<style>
.fc .fc-daygrid-event {
    display: block !important;
    white-space: normal !important;
    overflow-wrap: break-word !important;
    background-color: #fff !important;
    border: 1px solid #afac9b !important;
    border-radius: 8px !important;
    padding: 6px 8px !important;
    font-size: 0.92rem !important;
    line-height: 1.4 !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    color: #0e2442 !important;
}

.fc-event-time {
    font-weight: 600 !important;
    font-size: 0.85rem !important;
    color: #b44c4c !important;
    display: block !important;
    margin-bottom: 2px;
    border-bottom: 1px solid #ccc; /* ← this adds the separator */
    border-right: 1px solid #ccc; /* ← this adds the separator */
    border-radius: 5px;
    min-width: 50px;
}

.fc-event-title {
    font-weight: 600 !important;
    font-size: 0.92rem !important;
    display: block !important;
    white-space: normal !important;
    color: #0e2442 !important;
}

/* Optional: Style the event icon if you're using 🕒 */
.fc .fc-daygrid-event::before {
    margin-right: 4px;
    font-size: 0.85rem;
    color: #b44c4c;
}

.fc-daygrid-event-harness {
    width: 100% !important;
}
</style>