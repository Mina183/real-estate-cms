<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            ➕ Schedule New Meeting
        </h2>
    </x-slot>

    <div class="py-10 px-6 max-w-4xl mx-auto">
        @if(session('success'))
            <div class="mb-4 text-green-700 bg-green-100 border border-green-300 p-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('meetings.store') }}" method="POST" class="space-y-6 bg-white p-6 shadow rounded">
            @csrf

            <div>
                <label for="title" class="block font-semibold text-[#0e2442]">Meeting Title</label>
                <input type="text" name="title" id="title" required
                    class="w-full border-gray-300 rounded mt-1 focus:ring-[#0e2442] focus:border-[#0e2442]">
                @error('title')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block font-semibold text-[#0e2442]">Description (optional)</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full border-gray-300 rounded mt-1 focus:ring-[#0e2442] focus:border-[#0e2442]"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_time" class="block font-semibold text-[#0e2442]">Start Time</label>
                    <input type="datetime-local" name="start_time" id="start_time" required
                        class="w-full border-gray-300 rounded mt-1 focus:ring-[#0e2442] focus:border-[#0e2442]">
                    @error('start_time')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block font-semibold text-[#0e2442]">End Time</label>
                    <input type="datetime-local" name="end_time" id="end_time"
                        class="w-full border-gray-300 rounded mt-1 focus:ring-[#0e2442] focus:border-[#0e2442]">
                    @error('end_time')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="attendees" class="block font-semibold text-[#0e2442]">Invite Partners</label>
                <select name="attendees[]" id="attendees" multiple required
                    class="w-full border-gray-300 rounded mt-1 focus:ring-[#0e2442] focus:border-[#0e2442]">
                    @foreach($partners as $partner)
                        <option value="{{ $partner->id }}">{{ $partner->name }} ({{ $partner->email }})</option>
                    @endforeach
                </select>
                @error('attendees')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Browser timezone (used server-side to convert to UTC) --}}
                <input type="hidden" name="tz" id="meeting-tz-create" value="">
                <script>
                (function(){
                    var el = document.getElementById('meeting-tz-create');
                    if (el && window.Intl && Intl.DateTimeFormat) {
                    el.value = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
                    }
                })();
                </script>

            <div class="pt-4">
                <button type="submit"
                    class="bg-[#0e2442] text-white font-semibold px-6 py-2 rounded hover:bg-opacity-90 transition">
                    ✅ Create & Send Invites
                </button>
                <a href="{{ route('calendar.index') }}"
                   class="ml-4 text-[#0e2442] hover:underline font-semibold">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-app-layout>