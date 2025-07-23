<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            ✏️ Edit Meeting
        </h2>
        <div class="mt-2">
            <a href="{{ route('calendar.index') }}" class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                ← Back to Calendar
            </a>
        </div>
    </x-slot>

    <div class="py-10 px-6 max-w-3xl mx-auto">
        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('meetings.update', $meeting) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block font-semibold mb-1">Title</label>
                    <input type="text" name="title" value="{{ old('title', $meeting->title) }}" required
                           class="w-full border-gray-300 rounded px-3 py-2">
                </div>

                <div class="mb-4">
                    <label class="block font-semibold mb-1">Description</label>
                    <textarea name="description" class="w-full border-gray-300 rounded px-3 py-2"
                              rows="3">{{ old('description', $meeting->description) }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="block font-semibold mb-1">Start Time</label>
                    <input type="datetime-local" name="start_time"
                           value="{{ old('start_time', \Carbon\Carbon::parse($meeting->start_time)->format('Y-m-d\TH:i')) }}"
                           required class="w-full border-gray-300 rounded px-3 py-2">
                </div>

                <div class="mb-4">
                    <label class="block font-semibold mb-1">End Time</label>
                    <input type="datetime-local" name="end_time"
                           value="{{ old('end_time', $meeting->end_time ? \Carbon\Carbon::parse($meeting->end_time)->format('Y-m-d\TH:i') : '') }}"
                           class="w-full border-gray-300 rounded px-3 py-2">
                </div>

                <div class="mb-6">
                    <label class="block font-semibold mb-1">Invite Partners</label>
                    <select name="attendees[]" multiple required class="form-select">
                        @foreach ($partners as $partner)
                            <option value="{{ $partner->id }}"
                                {{ $meeting->attendees->contains($partner->id) ? 'selected' : '' }}>
                                {{ $partner->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label for="change_comment" class="block font-semibold mb-1">Comment on Change</label>
                    <textarea name="change_comment" id="change_comment" rows="3"
                            class="w-full border-gray-300 rounded px-3 py-2"
                            required
                            placeholder="Explain what was changed (optional)">
                        {{ old('change_comment', $meeting->change_comment) }}
                    </textarea>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit"
                            class="bg-[#0e2442] text-white px-5 py-2 rounded hover:bg-opacity-90 font-semibold">
                        💾 Update Meeting
                    </button>
                    </form>
                    <form action="{{ route('meetings.destroy', $meeting) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this meeting?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 font-semibold">
                            🗑 Delete
                        </button>
                    </form>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>